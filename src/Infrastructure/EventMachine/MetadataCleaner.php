<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\EventMachine;

use App\Api\Metadata;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\Commanding\CommandPreProcessor;

final class MetadataCleaner implements CommandPreProcessor
{
    private $cleanKeys = [
        Metadata::META_PASSWORD_HASHED,
        Metadata::META_USER_VALIDATED,
    ];


    /**
     * @inheritdoc
     */
    public function preProcess(Message $message): Message
    {
        $metadata = $message->metadata();

        foreach ($this->cleanKeys as $key) {
            if(array_key_exists($key, $metadata)) {
                unset($metadata[$key]);
            }
        }

        return $message->withMetadata($metadata);
    }
}