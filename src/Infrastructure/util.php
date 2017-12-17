<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Infrastructure;

use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageFactory;

function replace_payload(MessageFactory $messageFactory, Message $message, array $payload): Message {
    return $messageFactory->createMessageFromArray($message->messageName(), [
        'uuid' => $message->uuid(),
        'payload' => $payload,
        'metadata' => $message->metadata(),
        'created_at' => $message->createdAt(),
    ]);
}

function combine_regex_patterns(string $patternA, string $patternB, string ...$patterns): string {
    array_unshift($patterns, $patternA, $patternB);

    $combinedPattern = '';

    foreach ($patterns as $pattern) {
        $combinedPattern .= rtrim(ltrim($pattern, '^'), '$');
    }

    return '^' . $combinedPattern . '$';
};

//Exception factories
function message_not_allowed(Message $message, array $allowedMessages): \RuntimeException {
    return new \RuntimeException("Message not allowed. Got {$message->messageName()}, but allowed are only " . implode(", ", $allowedMessages));
};