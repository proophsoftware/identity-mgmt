<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Http;

use App\Api\Command;
use App\Api\PayloadFactory;
use function App\Infrastructure\now;
use App\Infrastructure\VerificationSession\VerificationSessionDescription;
use App\Model\VerificationSessionState;
use Interop\Http\Server\RequestHandlerInterface;
use Prooph\EventMachine\EventMachine;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\JsonResponse;

final class VerificationHandler implements RequestHandlerInterface
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    /**
     * @inheritdoc
     */
    public function handle(ServerRequestInterface $request)
    {
        try {
            if(!$verificationId = $request->getAttribute('verification', false)) {
                throw new \InvalidArgumentException("Missing verification id");
            }

            /** @var VerificationSessionState $verificationSession */
            $verificationSession = $this->eventMachine->loadAggregateState(
                VerificationSessionDescription::VERIFICATION_SESSION_AR,
                $verificationId
            );

            if($verificationSession->sessionExpiration()->isExpired(now())) {
                throw new \RuntimeException('Verification session expired');
            }

            $this->eventMachine->dispatch(Command::VERIFY_IDENTITY, PayloadFactory::makeVerifyIdentityPayload(
                $verificationSession->identityId()->toString(),
                $verificationSession->verificationId()->toString()
            ));

            //@TODO: Redirect to a verification ok page defined by user type schema
            return new JsonResponse(['verification' => true]);
        } catch (\Throwable $error) {
            //@TODO: Redirect to a verification failed page defined by user type schema
            throw $error;
        }
    }
}
