<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\VerificationSession;

use App\Api\Command;
use App\Api\Event;
use App\Api\MessageContext;
use App\Api\Payload;
use App\Model\VerificationSession;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class VerificationSessionDescription implements EventMachineDescription
{
    const VERIFICATION_SESSION_AR = MessageContext::CONTEXT . 'VerificationSession';

    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->on(Event::IDENTITY_ADDED, StartVerificationSession::class);

        $eventMachine->process(Command::START_VERIFICATION_SESSION)
            ->withNew(self::VERIFICATION_SESSION_AR)
            ->identifiedBy(Payload::KEY_VERIFICATION_ID)
            ->handle([VerificationSession::class, 'start'])
            ->recordThat(Event::VERIFICATION_SESSION_STARTED)
            ->apply([VerificationSession::class, 'whenVerificationSessionStarted']);
    }
}
