<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service;

use App\Http\MessageSchemaMiddleware;
use App\Http\Route;
use App\Infrastructure\EventMachine\MetadataCleaner;
use App\Infrastructure\Identity\AddIdentity;
use App\Infrastructure\Identity\EmailVerificationMailer;
use App\Infrastructure\Logger\PsrErrorLogger;
use App\Infrastructure\MongoDb\AggregateReadModel;
use App\Infrastructure\MongoDb\MongoConnection;
use App\Infrastructure\Password\PasswordHasher;
use App\Infrastructure\Password\PwdHashFuncHasher;
use App\Infrastructure\User\UserTypeIdInjector;
use App\Infrastructure\User\UserTypeSchemaValidator;
use App\Infrastructure\User\UserValidator;
use Codeliner\ArrayReader\ArrayReader;
use MongoDB\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\Common\Messaging\NoOpMessageConverter;
use Prooph\EventMachine\Container\ContainerChain;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\Container\ServiceRegistry;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\Http\MessageBox;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Pdo\PersistenceStrategy;
use Prooph\EventStore\Pdo\PostgresEventStore;
use Prooph\EventStore\Pdo\Projection\PostgresProjectionManager;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\TransactionalActionEventEmitterEventStore;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Message\HumusAmqp\AmqpMessageProducer;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\ErrorHandler;
use Zend\Stratigility\Middleware\ErrorResponseGenerator;

final class ServiceFactory
{
    use ServiceRegistry;

    /**
     * @var ArrayReader
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(array $appConfig)
    {
        $this->config = new ArrayReader($appConfig);
    }

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    //----- Command PreProcessor -----//

    public function userValidator(): UserValidator
    {
        return $this->makeSingleton(UserValidator::class, function () {
            return new UserTypeSchemaValidator($this->eventMachine());
        });
    }

    public function passwordHasher(): PasswordHasher
    {
        return $this->makeSingleton(PasswordHasher::class, function () {
            return new PwdHashFuncHasher($this->eventMachine()->messageFactory());
        });
    }

    public function metadataCleaner(): MetadataCleaner
    {
        return $this->makeSingleton(MetadataCleaner::class, function () {
            return new MetadataCleaner();
        });
    }

    public function userTypeInjector(): UserTypeIdInjector
    {
        return $this->makeSingleton(UserTypeIdInjector::class, function () {
            return new UserTypeIdInjector($this->eventMachine()->messageFactory());
        });
    }

    //----- Process Manager -----//
    public function addIdentity(): AddIdentity
    {
        return $this->makeSingleton(AddIdentity::class, function () {
            return new AddIdentity($this->eventMachine());
        });
    }

    public function emailVerificationMailer(): EmailVerificationMailer
    {
        return $this->makeSingleton(EmailVerificationMailer::class, function () {
            return new EmailVerificationMailer(
                $this->mailTransport(),
                $this->config->stringValue('mail.from'),
                $this->config->stringValue('mail.from_name'),
                $this->config->stringValue('base_url') . Route::VERIFICATION
            );
        });
    }

    public function httpMessageBox(): MessageBox
    {
        return $this->makeSingleton(MessageBox::class, function () {
            return $this->eventMachine()->httpMessageBox();
        });
    }

    public function eventMachineHttpMessageSchema(): MessageSchemaMiddleware
    {
        return $this->makeSingleton(MessageSchemaMiddleware::class, function () {
            return new MessageSchemaMiddleware($this->eventMachine());
        });
    }

    public function pdoConnection(): \PDO
    {
        return $this->makeSingleton(\PDO::class, function () {
            $this->assertMandatoryConfigExists('pdo.dsn');
            $this->assertMandatoryConfigExists('pdo.user');
            $this->assertMandatoryConfigExists('pdo.pwd');

            return new \PDO(
                $this->config->stringValue('pdo.dsn'),
                $this->config->stringValue('pdo.user'),
                $this->config->stringValue('pdo.pwd')
            );
        });
    }

    public function mongoConnection(): MongoConnection
    {
        return $this->makeSingleton(MongoConnection::class, function () {
            $this->assertMandatoryConfigExists('mongo.server');
            $this->assertMandatoryConfigExists('mongo.db');
            $client = new Client($this->config->stringValue('mongo.server'));
            return new MongoConnection($client, $this->config->stringValue('mongo.db'));
        });
    }

    protected function eventStorePersistenceStrategy(): PersistenceStrategy
    {
        return $this->makeSingleton(PersistenceStrategy::class, function () {
            return new PersistenceStrategy\PostgresSingleStreamStrategy();
        });
    }

    public function eventStore(): EventStore
    {
        return $this->makeSingleton(EventStore::class, function () {
            $eventStore = new PostgresEventStore(
                $this->eventMachine()->messageFactory(),
                $this->pdoConnection(),
                $this->eventStorePersistenceStrategy()
            );

            return new TransactionalActionEventEmitterEventStore(
                $eventStore,
                new ProophActionEventEmitter(TransactionalActionEventEmitterEventStore::ALL_EVENTS)
            );
        });
    }

    public function projectionManager(): ProjectionManager
    {
        return $this->makeSingleton(ProjectionManager::class, function () {
            return new PostgresProjectionManager(
                $this->eventStore(),
                $this->pdoConnection()
            );
        });
    }

    public function commandBus(): CommandBus
    {
        return $this->makeSingleton(CommandBus::class, function () {
            return new CommandBus();
        });
    }

    public function eventBus(): EventBus
    {
        return $this->makeSingleton(EventBus::class, function () {
            return new EventBus();
        });
    }

    public function aggregateReadModel(): AggregateReadModel
    {
        return $this->makeSingleton(AggregateReadModel::class, function () {
            return new AggregateReadModel($this->mongoConnection(), $this->eventMachine());
        });
    }

    public function uiExchange(): AmqpMessageProducer
    {
        return $this->makeSingleton(AmqpMessageProducer::class, function () {
           $this->assertMandatoryConfigExists('rabbit.connection');

            $connection = new \Humus\Amqp\Driver\AmqpExtension\Connection(
                $this->config->arrayValue('rabbit.connection')
            );

            $connection->connect();

            $channel = $connection->newChannel();

            $exchange = $channel->newExchange();

            $exchange->setName($this->config->stringValue('rabbit.ui_exchange', 'ui-exchange'));

            $exchange->setType('fanout');

            $humusProducer = new \Humus\Amqp\JsonProducer($exchange);

            $messageProducer = new \Prooph\ServiceBus\Message\HumusAmqp\AmqpMessageProducer(
                $humusProducer,
                new NoOpMessageConverter()
            );

            return $messageProducer;
        });
    }

    public function httpErrorHandler($environment = 'prod'): ErrorHandler
    {
        return $this->makeSingleton(ErrorHandler::class, function () {
            $errorHandler = new ErrorHandler(
                new Response(),
                new ErrorResponseGenerator($this->config->stringValue('environment', 'prod') === 'dev')
            );

            $errorHandler->attachListener(new PsrErrorLogger($this->logger()));

            return $errorHandler;
        });
    }

    public function logger(): LoggerInterface
    {
        return $this->makeSingleton(LoggerInterface::class, function () {
            $streamHandler = new StreamHandler('php://stderr');

            return new Logger('EventMachine', [$streamHandler]);
        });
    }

    public function mailTransport(): \Swift_Mailer
    {
        return $this->makeSingleton(\Swift_Mailer::class, function () {
            $transport =  (new \Swift_SmtpTransport(
                $this->config->stringValue('mail.smtp.host'),
                $this->config->integerValue('mail.smtp.port')
            ))
                ->setUsername($this->config->stringValue('mail.smtp.username'))
                ->setPassword($this->config->stringValue('mail.smtp.password'));

            if($this->config->mixedValue('mail.smtp.ssl')) {
                $transport->setEncryption($this->config->stringValue('mail.smtp.ssl'));
            }

            if($deliveryAddress = $this->config->mixedValue('mail.delivery_address')) {
                throw new \RuntimeException("@TODO activate swift mailer redirecting plugin");
            }

            return new \Swift_Mailer($transport);
        });
    }

    public function eventMachine(): EventMachine
    {
        $this->assertContainerIsset();

        return $this->makeSingleton(EventMachine::class, function () {
            //@TODO add config param to enable caching
            $eventMachine = new EventMachine();

            //Load descriptions here or add them to config/autoload/global.php
            foreach ($this->config->arrayValue('event_machine.descriptions') as $desc) {
                $eventMachine->load($desc);
            }

            $containerChain = new ContainerChain(
                $this->container,
                new EventMachineContainer($eventMachine)
            );

            $eventMachine->initialize($containerChain);

            return $eventMachine;
        });
    }

    private function assertContainerIsset(): void
    {
        if(null === $this->container) {
            throw new \RuntimeException("Main container is not set. Use " . __CLASS__ . "::setContainer() to set it.");
        }
    }

    private function assertMandatoryConfigExists(string $path): void
    {
        if(null === $this->config->mixedValue($path)) {
            throw  new \RuntimeException("Missing application config for $path");
        }
    }
}