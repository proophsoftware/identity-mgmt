<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Model\UserTypeSchema\UserTypeId;
use MongoDB\BSON\Serializable;
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

class UserTypeSchemaState implements ImmutableRecord, Serializable
{
    use ImmutableRecordLogic;

    /**
     * @var UserTypeId
     */
    private $typeId;

    /**
     * @var TenantId
     */
    private $tenantId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $schema;

    /**
     * @return UserTypeId
     */
    public function typeId(): UserTypeId
    {
        return $this->typeId;
    }

    /**
     * @return TenantId
     */
    public function tenantId(): TenantId
    {
        return $this->tenantId;
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
    public function schema(): array
    {
        return $this->schema;
    }

    /**
     * @inheritdoc
     */
    public function bsonSerialize()
    {
        return $this->toArray();
    }
}
