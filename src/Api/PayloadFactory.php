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

final class PayloadFactory
{
    public static function makeDefineUserTypeSchemaPayload(string $tenantId, string $type, array $schema, string $userTypeId = null): array {
        $p = [
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_SCHEMA => $schema
        ];

        if(null !== $userTypeId) {
            $p[Payload::KEY_TYPE_ID] = $userTypeId;
        }

        return $p;
    }

    public static function makeRegisterUserPayload(string $userId, string $tenantId, string $type, array $data, array $roles, string $email, string $password): array {
        return [
            Payload::KEY_TENANT_ID => $tenantId,
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_TYPE => $type,
            Payload::KEY_DATA => $data,
            Payload::KEY_ROLES => $roles,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password,
        ];
    }

    public static function makeAddIdentityPayload(string $tenantId, string $userId, string $email, string $password): array  {
        return [
            Payload::KEY_IDENTITY_ID => IdentityId::fromValues(TenantId::fromString($tenantId), Email::fromString($email))->toString(),
            Payload::KEY_USER_ID => $userId,
            Payload::KEY_EMAIL => $email,
            Payload::KEY_PASSWORD => $password
        ];
    }

    public static function makeVerifyIdentityPayload(string $identityId, string $verificationId): array {
        return [
            Payload::KEY_IDENTITY_ID => $identityId,
            Payload::KEY_VERIFICATION_ID => $verificationId,
        ];
    }

    public static function makeStartVerificationSessionPayload(string $verificationId, string $identityId): array {
        return [
            Payload::KEY_VERIFICATION_ID => $verificationId,
            Payload::KEY_IDENTITY_ID => $identityId,
        ];
    }
}
