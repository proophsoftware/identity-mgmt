<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

interface Command extends MessageContext
{
    //User and User Schema
    const DEFINE_USER_TYPE_SCHEMA = self::CONTEXT.'DefineUserTypeSchema';
    const REGISTER_USER = self::CONTEXT.'RegisterUser';
    //Identity
    const ADD_IDENTITY = self::CONTEXT.'AddIdentity';
    const VERIFY_IDENTITY = self::CONTEXT.'VerifyIdentity';
}