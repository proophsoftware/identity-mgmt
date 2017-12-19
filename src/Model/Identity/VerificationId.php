<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class VerificationId
{
    /**
     * @var UuidInterface
     */
    private $verificationId;

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }


    public static function fromString(string $verificationId): self
    {
        return new self(Uuid::fromString($verificationId));
    }

    private function __construct(UuidInterface $verificationId)
    {
        $this->verificationId = $verificationId;
    }

    public function toString(): string
    {
        return $this->verificationId->toString();
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->verificationId->equals($other->verificationId);
    }

    public function __toString(): string
    {
        return $this->verificationId->toString();
    }
}
