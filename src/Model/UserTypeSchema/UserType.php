<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\UserTypeSchema;

final class UserType
{
    private $type;

    public static function fromString(string $type): self
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        if(mb_strlen($type) < 3) {
            throw new \InvalidArgumentException("User type should be at least 3 chars long");
        }
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->type === $other->type;
    }

    public function __toString(): string
    {
        return $this->type;
    }
}
