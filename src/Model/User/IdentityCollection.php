<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Model\User;

use App\Model\Identity\IdentityId;

final class IdentityCollection
{
    /**
     * @var IdentityId[]
     */
    private $identityIds;

    public static function makeNew(): self
    {
        return new self();
    }

    public static function fromArray(array $data): self
    {
        $ids = array_map(function (string $id): IdentityId {
            return IdentityId::fromString($id);
        }, $data);

        return new self(...$ids);
    }

    private function __construct(IdentityId ...$identityIds)
    {
        foreach ($identityIds as $identityId) {
            $this->identityIds[$identityId->toString()] = $identityId;
        }
    }

    public function set(IdentityId $id): self
    {
        $cp = clone $this;
        $cp->identityIds[$id->toString()] = $id;
        return $cp;
    }

    public function remove(IdentityId $id): self
    {
        $cp = clone $this;
        unset($cp->identityIds[$id->toString()]);
        return $cp;
    }

    public function toArray(): array
    {
        return array_values(array_map(
            function (IdentityId $id): string {
                return $id->toString();
            },
            $this->identityIds
        ));
    }

    public function equals($other): bool
    {
        if(!$other instanceof self) {
            return false;
        }

        return $this->toArray() === $other->toArray();
    }

    public function __toString(): string
    {
        return json_encode($this->toArray());
    }

}