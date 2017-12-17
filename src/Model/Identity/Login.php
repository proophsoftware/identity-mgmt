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
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

class Login implements ImmutableRecord
{
    use ImmutableRecordLogic;

    /**
     * @var TenantId
     */
    private $tenantId;

    /**
     * @var Email
     */
    private $lowercaseEmail;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var bool
     */
    private $verified;

    public static function fromCredentials(TenantId $tenantId, Email $email, string $passwordHash): self
    {
        return self::fromArray([
            'tenantId' => $tenantId->toString(),
            'lowercaseEmail' => $email->toLowercase()->toString(),
            'passwordHash' => $passwordHash,
            'verified' => false
        ]);
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
        return $this->lowercaseEmail;
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
