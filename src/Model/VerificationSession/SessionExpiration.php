<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\VerificationSession;

final class SessionExpiration
{
    /**
     * @var \DateTimeImmutable
     */
    private $expiresAt;

    public static function in60Minutes(): self
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $interval = new \DateInterval('PT60M');
        return new self($now->add($interval));
    }

    public static function fromString(string $expiresAt): self
    {
        return new self(\DateTimeImmutable::createFromFormat(\DATE_ATOM, $expiresAt, new \DateTimeZone('UTC')));
    }

    private function __construct(\DateTimeImmutable $expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    public function isExpired(\DateTimeInterface $moment): bool
    {
        return $moment >= $this->expiresAt;
    }

    public function toString(): string
    {
        return $this->expiresAt->format(\DATE_ATOM);
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->expiresAt === $other->expiresAt;
    }

    public function __toString(): string
    {
        return $this->expiresAt;
    }
}
