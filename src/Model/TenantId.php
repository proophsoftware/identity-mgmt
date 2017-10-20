<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TenantId
{
    /**
     * @var UuidInterface
     */
    private $tenantId;

    public static function fromString(string $tenantId): self
    {
        return new self(Uuid::fromString($tenantId));
    }

    private function __construct(UuidInterface $tenantId)
    {
        $this->tenantId = $tenantId;
    }

    public function toString(): string
    {
        return $this->tenantId->toString();
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->tenantId->equals($other);
    }

    public function __toString(): string
    {
        return $this->tenantId->toString();
    }

}