<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Api;

use App\ProcessManager\UserValidation;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

class ProcessManagerDesc implements EventMachineDescription
{
    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->on(MsgDesc::EVT_USER_REGISTERED, UserValidation::class);
    }
}