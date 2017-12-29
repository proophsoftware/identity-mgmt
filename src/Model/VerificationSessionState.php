<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Model\Identity\IdentityId;
use App\Model\Identity\VerificationId;
use App\Model\VerificationSession\SessionExpiration;
use MongoDB\BSON\Serializable;
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

final class VerificationSessionState implements ImmutableRecord, Serializable
{
    use ImmutableRecordLogic;


    /**
     * @var VerificationId
     */
    private $verificationId;

    /**
     * @var IdentityId
     */
    private $identityId;

    /**
     * @var SessionExpiration
     */
    private $sessionExpiration;

    /**
     * @return VerificationId
     */
    public function verificationId(): VerificationId
    {
        return $this->verificationId;
    }

    /**
     * @return IdentityId
     */
    public function identityId(): IdentityId
    {
        return $this->identityId;
    }

    /**
     * @return SessionExpiration
     */
    public function sessionExpiration(): SessionExpiration
    {
        return $this->sessionExpiration;
    }

    /**
     * @inheritdoc
     */
    public function bsonSerialize()
    {
        return $this->toArray();
    }
}
