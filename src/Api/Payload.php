<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

interface Payload
{
    //Tenant Msg Keys
    const KEY_TENANT_ID = 'tenantId';

    //User Msg Keys
    const KEY_USER_ID = 'userId';
    const KEY_EMAIL = 'email';
    const KEY_PASSWORD = 'password';
    const KEY_ROLES = 'roles';
    const KEY_DATA = 'data';

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
    const KEY_VERIFICATION_ID = 'verificationId';
    //Verification Session
    const KEY_VERIFICATION_SESSION_EXPIRATION = 'sessionExpiration';
}
