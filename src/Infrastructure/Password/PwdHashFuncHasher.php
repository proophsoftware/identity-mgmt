<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Password;

use App\Api\MsgDesc;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;
use function App\Infrastructure\replace_payload;

final class PwdHashFuncHasher implements PasswordHasher
{
    /**
     * @var MessageFactory
     */
    private $messageFactory;

    public function __construct(MessageFactory $messageFactory)
    {
        $this->messageFactory = $messageFactory;
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

        $payload = $message->payload();

        $payload[MsgDesc::KEY_PASSWORD] = pwd_hash($payload[MsgDesc::KEY_PASSWORD]);

        return replace_payload($this->messageFactory, $message->withAddedMetadata(MsgDesc::META_PASSWORD_HASHED, true), $payload);
    }
}
