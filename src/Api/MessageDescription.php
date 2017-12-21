<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

use App\Model\Identity\Email;
use App\Model\Identity\IdentityId;
use App\Model\TenantId;
use App\Model\UserTypeSchema\UserTypeId;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;
use Ramsey\Uuid\Uuid;
use function App\Infrastructure\combine_regex_patterns;

final class MessageDescription implements EventMachineDescription
{
    public static function describe(EventMachine $eventMachine): void
    {
        //Misc
        $uuidSchema = ['type' => 'string', 'pattern' => Uuid::VALID_PATTERN];
        $tenantId = $uuidSchema;

        //User
        $userId = $uuidSchema;
        $email = ['type' => 'string', 'pattern' => Email::VALIDATION_PATTERN];
        $password = ['type' => 'string', 'minLength' => 8];
        $role = ['type' => 'string', 'minLength' => 3];
        $roles = ['type' => 'array', 'item' => $role];
        $data = ['type' => 'object', 'additionalProperties' => true];

        //UserTypeSchema
        $type = ['type' => 'string', 'minLength' => 3, 'pattern' => '^[\w]+$'];
        $typeId = ['type' => 'string', 'pattern' => combine_regex_patterns($type['pattern'], UserTypeId::DELIMITER, $uuidSchema['pattern'])];
        $schema = ['type' => 'object', 'additionalProperties' => true];

        //Identity
        $identityId = ['type' => 'string', 'pattern' => combine_regex_patterns(Email::VALIDATION_PATTERN, IdentityId::DELIMITER, $uuidSchema['pattern'])];
        $verification = JsonSchema::object([
            'identityId' => $identityId,
            'verificationId' => $uuidSchema
        ]);
        $verificationId = $uuidSchema;

        //Action: Define UserTypeSchema
        $eventMachine->registerCommand(Command::DEFINE_USER_TYPE_SCHEMA, JsonSchema::object([
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_SCHEMA => $schema,
        ], [
            Payload::KEY_TYPE_ID => $typeId,
        ]));

        $eventMachine->registerEvent(Event::USER_TYPE_SCHEMA_DEFINED, JsonSchema::object([
            Payload::KEY_TYPE_ID => $typeId,
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_SCHEMA => $schema
        ]));

        //Action: Register User
        $eventMachine->registerCommand(Command::REGISTER_USER, JsonSchema::object([
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_DATA => $data,
            Payload::KEY_ROLES => $roles,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password,
        ]));

        $eventMachine->registerEvent(Event::USER_REGISTERED, JsonSchema::object([
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_DATA => $data,
            Payload::KEY_ROLES => $roles,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password,
        ]));

        //Action Add Identity
        $eventMachine->registerCommand(Command::ADD_IDENTITY, JsonSchema::object([
            Payload::KEY_IDENTITY_ID => $identityId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password
        ]));

        $eventMachine->registerCommand(Command::VERIFY_IDENTITY, JsonSchema::object([
            Payload::KEY_IDENTITY_ID => $identityId,
            Payload::KEY_VERIFICATION_ID => $verificationId,
        ]));

        $eventMachine->registerEvent(Event::IDENTITY_ADDED, JsonSchema::object([
            Payload::KEY_IDENTITY_ID => $identityId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password,
            Payload::KEY_VERIFICATION => $verification,
        ]));

        $eventMachine->registerEvent(Event::IDENTITY_VERIFIED, JsonSchema::object([
            Payload::KEY_IDENTITY_ID => $identityId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_VERIFICATION => $verification,
        ]));
    }
}