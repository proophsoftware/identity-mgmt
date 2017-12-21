<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Identity;

use App\Api\Command;
use App\Api\Event;
use App\Api\MessageDescription;
use App\Api\Payload;
use App\Api\PayloadFactory;
use function App\Infrastructure\message_not_allowed;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;

final class AddIdentity
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    private $allowedMessages = [Event::USER_REGISTERED];

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    public function __invoke(Message $message)
    {
        if(!in_array($message->messageName(), $this->allowedMessages)) {
            throw message_not_allowed($message, $this->allowedMessages);
        }

        $this->eventMachine->dispatch(Command::ADD_IDENTITY, PayloadFactory::makeAddIdentityPayload(
            $message->payload()[Payload::KEY_TENANT_ID],
            $message->payload()[Payload::KEY_USER_ID],
            $message->payload()[Payload::KEY_EMAIL],
            $message->payload()[Payload::KEY_PASSWORD]
        ));
    }
}
