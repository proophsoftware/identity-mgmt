<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Api\MsgDesc;
use App\Model\Identity\Email;
use App\Model\Identity\IdentityId;
use App\Model\Identity\Verification;
use Prooph\Common\Messaging\Message;

final class Identity
{
    public static function add(Message $addIdentity): \Generator {
        $payload = $addIdentity->payload();
        $payload[MsgDesc::KEY_VERIFICATION] = Verification::initialize(self::newStateFromPayload($payload))->toArray();
        yield $payload;
    }

    public static function whenIdentityAdded(Message $identityAdded): IdentityState {
        return self::newStateFromPayload($identityAdded->payload());
    }

    private static function newStateFromPayload(array $payload): IdentityState
    {
        return IdentityState::newIdentity(
            IdentityId::fromString($payload[MsgDesc::KEY_IDENTITY_ID]),
            Email::fromString($payload[MsgDesc::KEY_EMAIL]),
            $payload[MsgDesc::KEY_PASSWORD]
        );
    }
}