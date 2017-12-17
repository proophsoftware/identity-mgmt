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
final class User
{
    public static function register(Message $registerUser): \Generator
    {
        if(!$registerUser->metadata()[MsgDesc::META_USER_VALIDATED] ?? false) {
            throw new \RuntimeException("User data was not validated by infrastructure. You should add a command preprocessor.");
        }

        if(!$registerUser->metadata()[MsgDesc::META_PASSWORD_HASHED] ?? false) {
            throw new \RuntimeException("Password was not hashed by infrastructure. You should add a command preprocessor.");
        }

        yield $registerUser->payload();
    }

    public static function whenUserRegistered(Message $userRegistered): UserState
    {
        $userData = $userRegistered->payload();

        unset($userData[MsgDesc::KEY_EMAIL]);
        unset($userData[MsgDesc::KEY_PASSWORD]);

        $userData['identities'] = [];

        return UserState::fromArray($userData);
    }
}
