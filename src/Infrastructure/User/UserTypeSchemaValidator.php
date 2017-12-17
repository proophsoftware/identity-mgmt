<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\User;

use App\Api\MsgDesc;
use App\Model\UserTypeSchemaState;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\EventMachine;

final class UserTypeSchemaValidator implements UserValidator
{
    /**
     * @var EventMachine
     */
    private $eventMachine;

    public function __construct(EventMachine $eventMachine)
    {
        $this->eventMachine = $eventMachine;
    }

    private $allowedMessages = [
        MsgDesc::CMD_REGISTER_USER,
    ];

    /**
     * @inheritdoc
     */
    public function preProcess(Message $message): Message
    {
        if(!in_array($message->messageName(), $this->allowedMessages)) {
            throw new \RuntimeException(__METHOD__ . " can only handle the messages: " . implode(', ', $this->allowedMessages));
        }

        /** @var UserTypeSchemaState $userTypeSchemaState */
        $userTypeSchemaState = $this->eventMachine->loadAggregateState(
            UserTypeSchemaDescription::USER_TYPE_SCHEMA_AR,
            $message->payload()[MsgDesc::KEY_TYPE]
        );

        $this->eventMachine->jsonSchemaAssertion()->assert(
            $message->messageName(),
            $message->payload()[MsgDesc::KEY_DATA],
            $userTypeSchemaState->schema()
        );

        return $message->withAddedMetadata(MsgDesc::META_USER_VALIDATED, true);
    }
}
