<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\User;

use App\Api\Command;
use App\Api\Event;
use App\Api\MessageContext;
use App\Api\Payload;
use App\Infrastructure\EventMachine\MetadataCleaner;
use App\Infrastructure\Password\PasswordHasher;
use App\Model\User;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class UserDescription implements EventMachineDescription
{
    const USER_AR = MessageContext::CONTEXT . 'User';

    public static function describe(EventMachine $eventMachine): void
    {
        self::registerUser($eventMachine);
    }

    private static function registerUser(EventMachine $eventMachine): void
    {
        //Make sure that verification metadata keys are not set from the outside
        $eventMachine->preProcess(Command::REGISTER_USER, MetadataCleaner::class);
        //Validate user data against UserTypeSchema
        $eventMachine->preProcess(Command::REGISTER_USER, UserValidator::class);
        //Hash pwd and add info in metadata
        $eventMachine->preProcess(Command::REGISTER_USER, PasswordHasher::class);

        $eventMachine->process(Command::REGISTER_USER)
            ->withNew(self::USER_AR)
            ->identifiedBy(Payload::KEY_USER_ID)
            ->handle([User::class, 'register'])
            ->recordThat(Event::USER_REGISTERED)
            ->apply([User::class, 'whenUserRegistered']);
    }
}
