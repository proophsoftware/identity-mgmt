<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

interface Metadata
{
    //User Meta Keys
    const META_PASSWORD_HASHED = 'passwordHashed';
    const META_USER_VALIDATED = 'userValidated';
}