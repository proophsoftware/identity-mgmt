<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Mock;

use App\Api\MsgDesc;
use App\Infrastructure\User\UserValidator;
use Prooph\Common\Messaging\Message;

class NoopUserValidator implements UserValidator
{

    /**
     * @inheritdoc
     */
    public function preProcess(Message $message): Message
    {
        return $message->withAddedMetadata(MsgDesc::META_USER_VALIDATED, true);
    }
}