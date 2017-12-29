<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Model\Identity\Email;
use App\Model\Identity\IdentityId;
use App\Model\Identity\Login;
use App\Model\User\UserId;
use MongoDB\BSON\Serializable;
use Prooph\EventMachine\Data\ImmutableRecord;
use Prooph\EventMachine\Data\ImmutableRecordLogic;

final class IdentityState implements ImmutableRecord, Serializable
{
    use ImmutableRecordLogic;

    /**
     * @var IdentityId
     */
    private $identityId;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var Login
     */
    private $login;

    public static function newIdentity(IdentityId $identityId, UserId $userId, Email $email, string $password): self
    {
        return self::fromArray([
            'identityId' => $identityId->toString(),
            'userId' => $userId->toString(),
            'email' => $email->toString(),
            'login' => Login::fromCredentials($identityId->tenantId(), $email, $password)->toArray(),
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
     * @return UserId
     */
    public function userId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return Email
     */
    public function email(): Email
    {
        return $this->email;
    }

    /**
     * @return Login
     */
    public function login(): Login
    {
        return $this->login;
    }

    /**
     * @inheritdoc
     */
    public function bsonSerialize()
    {
        return $this->toArray();
    }
}