<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Identity;

use App\Api\Command;
use App\Api\Event;
use App\Api\MessageContext;
use App\Api\Payload;
use App\Model\Identity;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class IdentityDescription implements EventMachineDescription
{
    public const IDENTITY_AR = MessageContext::CONTEXT . 'Identity';

    public static function describe(EventMachine $eventMachine): void {
        self::addIdentity($eventMachine);
        self::verifyIdentity($eventMachine);
    }

    public static function addIdentity(EventMachine $eventMachine): void {
        $eventMachine->on(Event::USER_REGISTERED, AddIdentity::class);

        $eventMachine->process(Command::ADD_IDENTITY)
            ->withNew(self::IDENTITY_AR)
            ->identifiedBy(Payload::KEY_IDENTITY_ID)
            ->handle([Identity::class, 'add'])
            ->recordThat(Event::IDENTITY_ADDED)
            ->apply([Identity::class, 'whenIdentityAdded']);

        $eventMachine->on(Event::IDENTITY_ADDED, EmailVerificationMailer::class);
    }

    public static function verifyIdentity(EventMachine $eventMachine): void {
        $eventMachine->process(Command::VERIFY_IDENTITY)
            ->withExisting(self::IDENTITY_AR)
            ->handle([Identity::class, 'verify'])
            ->recordThat(Event::IDENTITY_VERIFIED)
            ->apply([Identity::class, 'whenIdentityVerified']);
    }
}
