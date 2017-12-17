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
 * Class UserTypeSchema
 *
 * Users are validated against dynamic schemas based on assigned user type.
 *
 * This means that if a user has the type Admin then user data is validated against
 * the defined schema for the Admin type.
 *
 * @package App\Model
 */
class UserTypeSchema
{

    public static function define(Message $defineUserTypeSchema) {
        yield $defineUserTypeSchema->payload();
    }

    public static function whenUserTypeSchemaDefined(Message $userTypeSchemaDefined): UserTypeSchemaState {
        return UserTypeSchemaState::fromArray($userTypeSchemaDefined->payload());
    }
}