<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

interface Event extends MessageContext
{
    //User and User Schema
    const USER_TYPE_SCHEMA_DEFINED = self::CONTEXT.'UserTypeSchemaDefined';
    const USER_REGISTERED = self::CONTEXT.'UserRegistered';
    //Identity
    const IDENTITY_ADDED = self::CONTEXT.'IdentityAdded';
    const IDENTITY_VERIFIED = self::CONTEXT.'IdentityVerified';
    //Verification Session
    const VERIFICATION_SESSION_STARTED = self::CONTEXT.'VerificationSessionStarted';
}