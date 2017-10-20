<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\User;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserId
{
    /**
     * @var UuidInterface
     */
    private $userId;

    public static function fromString(string $userId): self
    {
        return new self(Uuid::fromString($userId));
    }

    private function __construct(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    public function toString(): string
    {
        return $this->userId->toString();
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->userId->equals($other);
    }

    public function __toString(): string
    {
        return $this->userId->toString();
    }

}