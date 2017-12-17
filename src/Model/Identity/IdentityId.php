<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

use App\Model\TenantId;

class IdentityId
{
    public const DELIMITER = ':::';

    private $id;

    /**
     * @var TenantId
     */
    private $tenantId;

    /**
     * @var Email
     */
    private $email;

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public static function fromValues(TenantId $tenantId, Email $email): self
    {
        return self::fromString(self::generateId($tenantId, $email));
    }

    public static function generateId(TenantId $tenantId, Email $email): string
    {
        return $email->toLowercase()->toString() . self::DELIMITER . $tenantId->toString();
    }

    public static function extractTenantId(string $identityId): TenantId
    {
        $parts = explode(self::DELIMITER, $identityId);

        if(count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid IdentityId. Got $identityId");
        }

        return TenantId::fromString($parts[1]);
    }

    public static function extractLowercaseEmail(string $identityId): Email
    {
        $parts = explode(self::DELIMITER, $identityId);

        if(count($parts) !== 2) {
            throw new \InvalidArgumentException("Invalid IdentityId. Got $identityId");
        }

        return Email::fromString($parts[0]);
    }

    private function __construct(string $id)
    {
        $this->tenantId = self::extractTenantId($id);
        $this->email = self::extractLowercaseEmail($id);
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
     * @return Email
     */
    public function lowercaseEmail(): Email
    {
        return $this->email;
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
