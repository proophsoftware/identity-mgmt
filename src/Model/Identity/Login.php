<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

use App\Infrastructure\Util\VOArray;

class Login
{
    use VOArray;

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
        $self = new self();
        $self->email = $email;
        $self->passwordHash = $passwordHash;
        $self->verified = false;
        return $self;
    }

    public static function fromArray(array $data): self
    {
        $self = new self();
        $self->mergeProps($self, $data);
        return $self;
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
    public function isVerified(): bool
    {
        return $this->verified;
    }

    public function markAsVerified(): self
    {
        $cp = clone $this;
        $cp->verified = true;
        return $cp;
    }

    private function __construct()
    {
    }
}