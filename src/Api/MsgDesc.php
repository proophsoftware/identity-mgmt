<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;
use Prooph\EventMachine\JsonSchema\JsonSchema;
use Ramsey\Uuid\Uuid;
use function App\Infrastructure\combine_regex_patterns;

final class MsgDesc implements EventMachineDescription
{
    const CONTEXT = 'Identity.';

    const CMD_DEFINE_USER_TYPE_SCHEMA = self::CONTEXT.'DefineUserTypeSchema';
    const CMD_REGISTER_USER = self::CONTEXT.'RegisterUser';
    const CMD_ADD_IDENTITY = self::CONTEXT.'AddIdentity';

    const EVT_USER_TYPE_SCHEMA_DEFINED = self::CONTEXT.'UserTypeSchemaDefined';
    const EVT_USER_REGISTERED = self::CONTEXT.'UserRegistered';
    const EVT_IDENTITY_ADDED = self::CONTEXT.'IdentityAdded';

    //Tenant Msg Keys
    const KEY_TENANT_ID = 'tenantId';

    //User Msg Keys
    const KEY_USER_ID = 'userId';
    const KEY_EMAIL = 'email';
    const KEY_PASSWORD = 'password';
    const KEY_ROLES = 'roles';
    const KEY_DATA = 'data';

    //User Meta Keys
    const META_PASSWORD_HASHED = 'passwordHashed';
    const META_USER_VALIDATED = 'userValidated';

    //UserTypeSchema Msg Keys
    const KEY_TYPE_ID = 'typeId';
    const KEY_TYPE = 'type';
    const KEY_SCHEMA = 'schema';

    public static function describe(EventMachine $eventMachine): void
    {
        //Misc
        $uuidSchema = ['type' => 'string', 'pattern' => Uuid::VALID_PATTERN];
        $tenantId = $uuidSchema;

        //User
        $userId = $uuidSchema;
        $email = ['type' => 'string', 'format' => 'email'];
        $password = ['type' => 'string', 'minLength' => 8];
        $role = ['type' => 'string', 'minLength' => 3];
        $roles = ['type' => 'array', 'item' => $role];
        $data = ['type' => 'object', 'additionalProperties' => true];

        //UserTypeSchema
        $type = ['type' => 'string', 'minLength' => 3, 'pattern' => '^[\w]+$'];
        $typeId = ['type' => 'string', 'pattern' => combine_regex_patterns($type['pattern'], ':::', $uuidSchema['pattern'])];
        $schema = ['type' => 'object', 'additionalProperties' => true];

        //Action: Define UserTypeSchema
        $eventMachine->registerCommand(self::CMD_DEFINE_USER_TYPE_SCHEMA, JsonSchema::object([
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_TYPE => $type,
            self::KEY_SCHEMA => $schema,
        ], [
            self::KEY_TYPE_ID => $typeId,
        ]));

        $eventMachine->registerEvent(self::EVT_USER_TYPE_SCHEMA_DEFINED, JsonSchema::object([
            self::KEY_TYPE_ID => $typeId,
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_TYPE => $type,
            self::KEY_SCHEMA => $schema
        ]));

        //Action: Register User
        $eventMachine->registerCommand(self::CMD_REGISTER_USER, JsonSchema::object([
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_USER_ID => $userId,
            self::KEY_TYPE => $type,
            self::KEY_DATA => $data,
            self::KEY_ROLES => $roles,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password,
        ]));

        $eventMachine->registerEvent(self::EVT_USER_REGISTERED, JsonSchema::object([
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_USER_ID => $userId,
            self::KEY_TYPE => $type,
            self::KEY_DATA => $data,
            self::KEY_ROLES => $roles,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password,
        ]));
    }
}