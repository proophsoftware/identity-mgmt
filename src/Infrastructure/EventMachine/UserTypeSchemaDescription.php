<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\EventMachine;

use App\Api\MsgDesc;
use App\Model\UserTypeSchema;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class UserTypeSchemaDescription implements EventMachineDescription
{
    const USER_ROLE_SCHEMA_AR = MsgDesc::CONTEXT . 'UserRoleSchema';

    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->process(MsgDesc::CMD_DEFINE_USER_TYPE_SCHEMA)
            ->withNew(self::USER_ROLE_SCHEMA_AR)
            ->identifiedBy(MsgDesc::KEY_TYPE)
            ->handle([UserTypeSchema::class, 'define'])
            ->recordThat(MsgDesc::EVT_USER_TYPE_SCHEMA_DEFINED)
            ->apply([UserTypeSchema::class, 'whenUserTypeSchemaDefined']);
    }
}