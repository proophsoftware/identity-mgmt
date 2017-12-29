<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest;

use App\Api\Command;
use App\Api\Event;
use App\Api\Payload;
use App\Api\PayloadFactory;
use App\Infrastructure\EventMachine\MetadataCleaner;
use App\Infrastructure\Password\PasswordHasher;
use App\Infrastructure\Password\PwdHashFuncHasher;
use App\Infrastructure\User\UserTypeIdInjector;
use App\Infrastructure\User\UserValidator;
use App\Model\Identity\Email;
use App\Model\Identity\IdentityId;
use App\Model\Identity\Login;
use App\Model\Identity\Verification;
use App\Model\Identity\VerificationId;
use App\Model\IdentityState;
use App\Model\TenantId;
use App\Model\User\UserId;
use App\Model\UserTypeSchema\UserType;
use App\Model\UserTypeSchema\UserTypeId;
use AppTest\Mock\NoopPasswordHasher;
use AppTest\Mock\NoopUserValidator;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\EventMachine;

class BaseTestCase extends TestCase
{
    const TYPE_ADMIN = 'Admin';
    const TYPE_EDITOR = 'Editor';
    const ROLE_ADMIN = 'Admin';
    const ROLE_EDITOR = 'Editor';

    const EDITOR_SCHEMA = [
        'type' => 'object',
        'properties' => [
            'level' => [
                'enum' => ['guest', 'regular', 'trusted', 'member']
            ]
        ]
    ];

    /**
     * @var EventMachine
     */
    protected $eventMachine;

    /**
     * @var TenantId
     */
    protected $tenantId;

    /**
     * @var UserId
     */
    protected $userId;

    /**
     * @var IdentityState
     */
    protected $adminIdentity;

    /**
     * @var Login
     */
    protected $adminLogin;

    protected function setUp()
    {
        $this->eventMachine = new EventMachine();

        $config = include __DIR__ . '/../config/autoload/global.php';

        foreach ($config['event_machine']['descriptions'] as $description) {
            $this->eventMachine->load($description);
        }

        $this->eventMachine->initialize(new EventMachineContainer($this->eventMachine));

        $email = Email::fromString('admin@prooph.local');
        $passwordHash = '$2y$10$9s6ahvJfptN/m3BRcl6oJONqvwU0fQeif5KUDuzR259gcOyZqKfRO';

        $this->tenantId = TenantId::fromString('03c8d742-1bed-46d1-a985-080b9a036656');
        $this->userId = UserId::fromString('42282edf-9007-4218-b3f8-931580d1abd0');
        $this->adminIdentity = IdentityState::newIdentity(
            IdentityId::fromValues($this->tenantId, $email),
            $this->userId,
            $email,
            $passwordHash
        );
        $this->adminLogin = Login::fromArray([
            'tenantId' => $this->tenantId->toString(),
            'lowercaseEmail' => $email->toLowercase()->toString(),
            'passwordHash' => $passwordHash,
            'verified' => true
        ]);
    }

    protected function tearDown()
    {
        $this->eventMachine = null;
    }

    protected function registerUser(
        string $type = self::TYPE_ADMIN,
        array $roles = [self::ROLE_ADMIN],
        array $data = ['username' => 'sudo']
    ): Message
    {
        return $this->buildCmd(Command::REGISTER_USER, [
            Payload::KEY_TENANT_ID => $this->tenantId->toString(),
            Payload::KEY_USER_ID => $this->userId->toString(),
            Payload::KEY_TYPE => $type,
            Payload::KEY_ROLES => $roles,
            Payload::KEY_DATA => $data,
            Payload::KEY_EMAIL => $this->adminLogin->lowercaseEmail()->toString(),
            Payload::KEY_PASSWORD => 'my_secret',
        ]);
    }

    protected function userRegistered(
        string $type = self::TYPE_ADMIN,
        array $roles = [self::ROLE_ADMIN],
        array $data = ['username' => 'sudo']
    ): Message
    {
        return $this->buildEvent(Event::USER_REGISTERED, [
            Payload::KEY_TENANT_ID => $this->tenantId->toString(),
            Payload::KEY_USER_ID => $this->userId->toString(),
            Payload::KEY_TYPE => $type,
            Payload::KEY_ROLES => $roles,
            Payload::KEY_DATA => $data,
            Payload::KEY_EMAIL => $this->adminLogin->lowercaseEmail()->toString(),
            Payload::KEY_PASSWORD => 'my_secret',
        ]);
    }

    protected function defineUserTypeSchema(string $type = self::TYPE_EDITOR, array $schema = self::EDITOR_SCHEMA): Message
    {
        return $this->buildCmd(Command::DEFINE_USER_TYPE_SCHEMA, PayloadFactory::makeDefineUserTypeSchemaPayload(
            $this->tenantId,
            $type,
            $schema
        ));
    }

    protected function userTypeSchemaDefined(string $type = self::TYPE_EDITOR, array $schema = self::EDITOR_SCHEMA): Message
    {
        return $this->buildEvent(Event::USER_TYPE_SCHEMA_DEFINED, [
            Payload::KEY_TYPE_ID => UserTypeId::fromValues(
                $this->tenantId,
                UserType::fromString($type)
            )->toString(),
            Payload::KEY_TENANT_ID => $this->tenantId->toString(),
            Payload::KEY_TYPE => $type,
            Payload::KEY_SCHEMA => $schema,
        ]);
    }

    protected function addIdentity(string $email = null, string $password = null): Message
    {
        return $this->buildCmd(Command::ADD_IDENTITY, PayloadFactory::makeAddIdentityPayload(
            $this->tenantId->toString(),
            $this->userId->toString(),
            $email? : $this->adminLogin->lowercaseEmail()->toString(),
            $password? : $this->adminLogin->passwordHash()
        ));
    }

    protected function identityAdded(string $email = null, string $password = null): Message
    {
        if(!is_null($email) || !is_null($password)) {
            $email = Email::fromString($email? : $this->adminIdentity->email()->toString());

            $identity = IdentityState::newIdentity(
                IdentityId::fromValues($this->tenantId, $email),
                $this->userId,
                $email,
                $password? : $this->adminIdentity->login()->passwordHash()
            );
        } else {
            $identity = $this->adminIdentity;
        }

        return $this->buildEvent(Event::IDENTITY_ADDED, [
            Payload::KEY_IDENTITY_ID => $identity->identityId()->toString(),
            Payload::KEY_USER_ID => $this->userId->toString(),
            Payload::KEY_EMAIL => $identity->email()->toString(),
            Payload::KEY_PASSWORD => $identity->login()->passwordHash(),
            Payload::KEY_VERIFICATION => Verification::initialize($identity)->toArray()
        ]);
    }

    protected function startVerificationSession(): Message
    {
        return $this->buildCmd(Command::START_VERIFICATION_SESSION, [
            Payload::KEY_VERIFICATION_ID => VerificationId::generate()->toString(),
            Payload::KEY_IDENTITY_ID => $this->adminIdentity->identityId()->toString(),
        ]);
    }

    protected function getRegisterUserServices($mockPasswordHasher = true): array
    {
        return [
            MetadataCleaner::class => new MetadataCleaner(),
            UserValidator::class => new NoopUserValidator(),
            PasswordHasher::class => $mockPasswordHasher?
                new NoopPasswordHasher()
                //Note: the prod service is slow, use the mocked service by default
                : new PwdHashFuncHasher($this->eventMachine->messageFactory()),
        ];
    }

    protected function getDefineUserTypeSchemaServices(): array
    {
        return [
            UserTypeIdInjector::class => new UserTypeIdInjector($this->eventMachine->messageFactory())
        ];
    }

    protected function buildCmd(string $cmdName, array $payload, array $metadata = []): Message
    {
        return $this->buildMessage($cmdName, $payload, $metadata);
    }

    protected function buildEvent(string $evtName, array $payload, array $metadata = []): Message
    {
        return $this->buildMessage($evtName, $payload, $metadata);
    }

    protected function buildMessage(string $msgName, array $payload, array $metadata = []): Message
    {
        return $this->eventMachine->messageFactory()->createMessageFromArray($msgName, [
            'payload' => $payload,
            'metadata' => $metadata
        ]);
    }
}