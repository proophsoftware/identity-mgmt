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
use function App\Infrastructure\Password\pwd_hash;
use Prooph\Common\Messaging\Message;

/**
 * Class User
 *
 * User is the central aggregate of the Identity Mgmt context. It has a type which defines
 * the schema of the user data (customizable) and is connected with one or more identities.
 *
 * A user also has one or more roles that are used for role based access control (RBAC)
 * and belongs to groups to get access to different applications.
 *
 * @package App\Model
 */
class User
{
    public static function register(Message $registerUser)
    {
        $payload = $registerUser->payload();
        $payload['password'] = pwd_hash($payload['password']);
        $payload['validated'] = false;
        yield $payload;
    }

    public static function whenUserRegistered(Message $userRegistered): UserState
    {
        $state = new UserState();

        $state->tenantId = $userRegistered->payload()[MsgDesc::KEY_TENANT_ID];
        $state->userId = $userRegistered->payload()[MsgDesc::KEY_USER_ID];
        $state->type = $userRegistered->payload()[MsgDesc::KEY_TYPE];
        $state->roles = $userRegistered->payload()[MsgDesc::KEY_ROLES];
        $state->data = $userRegistered->payload()[MsgDesc::KEY_DATA];
        $state->validated = $userRegistered->payload()[MsgDesc::KEY_VALIDATED];

        return $state;
    }
}