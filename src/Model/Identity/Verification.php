<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\Identity;

use App\Model\IdentityState;
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

final class Verification implements ImmutableRecord
{
    use ImmutableRecordLogic;

    /**
     * @var IdentityId
     */
    private $identityId;

    /**
     * @var VerificationId
     */
    private $verificationId;

    public static function initialize(IdentityState $identity): self
    {
        return self::fromRecordData([
            'identityId' => $identity->identityId(),
            'verificationId' => VerificationId::generate()
        ]);
    }

    /**
     * @return IdentityId
     */
    public function identityId(): IdentityId
    {
        return $this->identityId;
    }

    /**
     * @return VerificationId
     */
    public function verificationId(): VerificationId
    {
        return $this->verificationId;
    }
}