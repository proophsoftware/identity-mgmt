<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

final class Email
{
    //Taken from: http://www.regular-expressions.info/email.html
    //Note: Please read the "Regexes Don’t Send Email" section ^^
    //We work with verification, so our regex is kept simple, too.
    public const VALIDATION_PATTERN = '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,63}$';

    private $email;

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    private function __construct(string $email)
    {
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Email is not valid. Got " . $email);
        }
        $this->email = $email;
    }

    public function toString(): string
    {
        return $this->email;
    }

    public function toLowercase(): self
    {
        return self::fromString(mb_strtolower($this->toString()));
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return mb_strtolower($this->email) === mb_strtolower($other->email);
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
