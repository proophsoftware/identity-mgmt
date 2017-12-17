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
use function App\Infrastructure\replace_payload;
use App\Model\TenantId;
use App\Model\UserTypeSchema\UserType;
use App\Model\UserTypeSchema\UserTypeId;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use Prooph\EventMachine\Commanding\CommandPreProcessor;

class UserTypeIdInjector implements CommandPreProcessor
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(MessageFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    /**
     * @inheritdoc
     */
    public function preProcess(Message $message): Message
    {
        if($message->messageName() !== MsgDesc::CMD_DEFINE_USER_TYPE_SCHEMA) {
            return $message;
        }

        $payload = $message->payload();
        $payload[MsgDesc::KEY_TYPE_ID] = UserTypeId::fromValues(
            TenantId::fromString($payload[MsgDesc::KEY_TENANT_ID]),
            UserType::fromString($payload[MsgDesc::KEY_TYPE])
        )->toString();

        return replace_payload($this->messageFactory, $message, $payload);
    }
}