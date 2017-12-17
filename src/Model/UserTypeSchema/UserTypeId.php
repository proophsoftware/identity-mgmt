<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\UserTypeSchema;

use App\Model\TenantId;

final class UserTypeId
{
    public const DELIMITER = ':::';
    /**
     * @var TenantId
     */
    private $tenantId;

    /**
     * @var UserType
     */
    private $userType;

    private $id;

    public static function fromValues(TenantId $tenantId, UserType $userType): self
    {
        return self::fromString(self::generateId($tenantId, $userType));
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public static function generateId(TenantId $tenantId, UserType $userType): string
    {
        return $userType->toString() . self::DELIMITER . $tenantId->toString();
    }

    public static function extractUserType(string $userTypeId): UserType
    {
        $parts = explode(self::DELIMITER, $userTypeId);

        if(count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid UserTypeId. Got $userTypeId");
        }

        return UserType::fromString($parts[0]);
    }

    public static function extractTenantId(string $userTypeId): TenantId
    {
        $parts = explode(self::DELIMITER, $userTypeId);

        if(count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid UserTypeId. Got $userTypeId");
        }

        return TenantId::fromString($parts[1]);
    }

    private function __construct(string $id)
    {
        $this->tenantId = self::extractTenantId($id);
        $this->userType = self::extractUserType($id);
        $this->id = $id;
    }

    /**
     * @return TenantId
     */
    public function tenantId(): TenantId
    {
        return $this->tenantId;
    }

    /**
     * @return UserType
     */
    public function userType(): UserType
    {
        return $this->userType;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->id === $other->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}