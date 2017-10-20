<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

class UserState
{
    /**
     * @var string
     */
    public $tenantId;

    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $roles;

    /**
     * @var array
     */
    public $data;

    /**
     * @var array
     */
    public $identities;

    /**
     * @var boolean
     */
    public $validated;
}