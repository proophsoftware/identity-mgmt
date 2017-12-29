<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\VerificationSession;

use App\Api\Command;
use App\Api\Event;
use App\Api\Payload;
use App\Api\PayloadFactory;
use function App\Infrastructure\assert_allowed_message;
use App\Model\Identity\Verification;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;

final class StartVerificationSession
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    private $allowedMessages = [Event::IDENTITY_ADDED];

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    public function __invoke(Message $identityAdded)
    {
        assert_allowed_message($identityAdded, $this->allowedMessages);

        $this->eventMachine->dispatch(Command::START_VERIFICATION_SESSION, PayloadFactory::makeStartVerificationSessionPayload(
            Verification::fromArray($identityAdded->payload()[Payload::KEY_VERIFICATION])->verificationId()->toString(),
            $identityAdded->payload()[Payload::KEY_IDENTITY_ID]
        ));
    }
}
