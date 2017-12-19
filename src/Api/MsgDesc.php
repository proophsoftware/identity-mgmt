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

    //Identity
    const KEY_IDENTITY_ID = 'identityId';
    //const KEY_USER_ID = 'userId';
    //const KEY_EMAIL = 'email';
    //const KEY_PASSWORD = 'password';
    const KEY_VERIFICATION = 'verification';

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

        //Action Add Identity
        $eventMachine->registerCommand(self::CMD_ADD_IDENTITY, JsonSchema::object([
            self::KEY_IDENTITY_ID => $identityId,
            self::KEY_USER_ID => $userId,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password
        ]));

        $eventMachine->registerEvent(self::EVT_IDENTITY_ADDED, JsonSchema::object([
            self::KEY_IDENTITY_ID => $identityId,
            self::KEY_USER_ID => $userId,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password,
            self::KEY_VERIFICATION => $verification,
        ]));
    }

    public static function defineUserTypeSchemaPayload(string $tenantId, string $type, array $schema, string $userTypeId = null): array {
        $p = [
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_TYPE => $type,
            self::KEY_SCHEMA => $schema
        ];

        if(null !== $userTypeId) {
            $p[self::KEY_TYPE_ID] = $userTypeId;
        }

        return $p;
    }

    public static function registerUserPayload(string $userId, string $tenantId, string $type, array $data, array $roles, string $email, string $password): array {
        return [
            self::KEY_TENANT_ID => $tenantId,
            self::KEY_USER_ID => $userId,
            self::KEY_TYPE => $type,
            self::KEY_DATA => $data,
            self::KEY_ROLES => $roles,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password,
        ];
    }

    public static function addIdentityPayload(string $tenantId, string $userId, string $email, string $password): array  {
        return [
            self::KEY_IDENTITY_ID => IdentityId::fromValues(TenantId::fromString($tenantId), Email::fromString($email))->toString(),
            self::KEY_USER_ID => $userId,
            self::KEY_EMAIL => $email,
            self::KEY_PASSWORD => $password
        ];
    }
}