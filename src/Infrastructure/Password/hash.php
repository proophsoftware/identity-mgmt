<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Infrastructure\Password;

function pwd_hash(string $password): string {
    $algo = getenv('PWD_HASH_ALGO');
    $cost = getenv('PWD_HASH_COST');

    if(false === $algo) {
        $algo = PASSWORD_DEFAULT;
    }

    if(false === $cost) {
        $cost = 12;
    }

    return password_hash($password, (int)$algo, ['cost' => (int)$cost]);
}

function pwd_verify(string $password, string $hash): bool {
    return password_verify($password, $hash);
}
