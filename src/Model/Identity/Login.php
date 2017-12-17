<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

class Login implements ImmutableRecord
{
    use ImmutableRecordLogic;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var bool
     */
    private $verified;

    public static function fromCredentials(string $email, string $passwordHash): self
    {
        return self::fromArray([
            'email' => $email,
            'passwordHash' => $passwordHash,
            'verified' => false
        ]);
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function passwordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return bool
     */
    public function verified(): bool
    {
        return $this->verified;
    }

    public function markAsVerified(): self
    {
        $cp = clone $this;
        $cp->verified = true;
        return $cp;
    }
}