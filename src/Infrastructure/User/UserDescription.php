<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\User;

use App\Api\MsgDesc;
use App\Infrastructure\EventMachine\MetadataCleaner;
use App\Infrastructure\Password\PasswordHasher;
use App\Model\User;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class UserDescription implements EventMachineDescription
{
    const USER_AR = MsgDesc::CONTEXT . 'User';

    public static function describe(EventMachine $eventMachine): void
    {
        self::registerUser($eventMachine);
    }

    private static function registerUser(EventMachine $eventMachine): void
    {
        //Make sure that verification metadata keys are not set from the outside
        $eventMachine->preProcess(MsgDesc::CMD_REGISTER_USER, MetadataCleaner::class);
        //Validate user data against UserTypeSchema
        $eventMachine->preProcess(MsgDesc::CMD_REGISTER_USER, UserValidator::class);
        //Hash pwd and add info in metadata
        $eventMachine->preProcess(MsgDesc::CMD_REGISTER_USER, PasswordHasher::class);

        $eventMachine->process(MsgDesc::CMD_REGISTER_USER)
            ->withNew(self::USER_AR)
            ->identifiedBy(MsgDesc::KEY_USER_ID)
            ->handle([User::class, 'register'])
            ->recordThat(MsgDesc::EVT_USER_REGISTERED)
            ->apply([User::class, 'whenUserRegistered']);
    }
}
