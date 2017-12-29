<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Model\User\IdentityCollection;
use App\Model\User\UserId;
use MongoDB\BSON\Serializable;
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

final class UserState implements ImmutableRecord, Serializable
{
    use ImmutableRecordLogic;

    /**
     * @var TenantId
     */
    private $tenantId;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var array
     */
    private $data;

    /**
     * @var IdentityCollection
     */
    private $identities;

    /**
     * @return TenantId
     */
    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    /**
     * @return UserId
     */
    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function roles(): array
    {
        return $this->roles;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @return IdentityCollection
     */
    public function identities(): IdentityCollection
    {
        return $this->identities;
    }

    /**
     * @inheritdoc
     */
    public function bsonSerialize()
    {
        return $this->toArray();
    }
}