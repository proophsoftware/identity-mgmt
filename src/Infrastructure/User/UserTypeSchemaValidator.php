<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\User;

use App\Api\Command;
use App\Api\Metadata;
use App\Api\Payload;
use function App\Infrastructure\assert_allowed_message;
use App\Model\TenantId;
use App\Model\UserTypeSchema\UserType;
use App\Model\UserTypeSchema\UserTypeId;
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
        Command::REGISTER_USER,
    ];

    /**
     * @inheritdoc
     */
    public function preProcess(Message $message): Message
    {
        assert_allowed_message($message, $this->allowedMessages);

        /** @var UserTypeSchemaState $userTypeSchemaState */
        $userTypeSchemaState = $this->eventMachine->loadAggregateState(
            UserTypeSchemaDescription::USER_TYPE_SCHEMA_AR,
            UserTypeId::fromValues(
                TenantId::fromString($message->payload()[Payload::KEY_TENANT_ID]),
                UserType::fromString($message->payload()[Payload::KEY_TYPE])
            )
        );

        $this->eventMachine->jsonSchemaAssertion()->assert(
            $message->messageName(),
            $message->payload()[Payload::KEY_DATA],
            $userTypeSchemaState->schema()
        );

        return $message->withAddedMetadata(Metadata::META_USER_VALIDATED, true);
    }
}
