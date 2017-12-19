<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Identity;

use App\Api\MsgDesc;
use App\Model\Identity;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class IdentityDescription implements EventMachineDescription
{
    public const IDENTITY_AR = MsgDesc::CONTEXT . 'Identity';

    public static function describe(EventMachine $eventMachine): void
    {
        self::addIdentity($eventMachine);
    }

    public static function addIdentity(EventMachine $eventMachine): void
    {
        $eventMachine->on(MsgDesc::EVT_USER_REGISTERED, AddIdentity::class);

        $eventMachine->process(MsgDesc::CMD_ADD_IDENTITY)
            ->withNew(self::IDENTITY_AR)
            ->identifiedBy(MsgDesc::KEY_IDENTITY_ID)
            ->handle([Identity::class, 'add'])
            ->recordThat(MsgDesc::EVT_IDENTITY_ADDED)
            ->apply([Identity::class, 'whenIdentityAdded']);

        $eventMachine->on(MsgDesc::EVT_IDENTITY_ADDED, EmailVerificationMailer::class);
    }
}
