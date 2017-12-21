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
use App\Model\UserTypeSchema;
use Prooph\EventMachine\EventMachine;
use Prooph\EventMachine\EventMachineDescription;

final class UserTypeSchemaDescription implements EventMachineDescription
{
    const USER_TYPE_SCHEMA_AR = MessageContext::CONTEXT . 'UserTypeSchema';

    public static function describe(EventMachine $eventMachine): void
    {
        $eventMachine->preProcess(Command::DEFINE_USER_TYPE_SCHEMA, UserTypeIdInjector::class);

        $eventMachine->process(Command::DEFINE_USER_TYPE_SCHEMA)
            ->withNew(self::USER_TYPE_SCHEMA_AR)
            ->identifiedBy(Payload::KEY_TYPE_ID)
            ->handle([UserTypeSchema::class, 'define'])
            ->recordThat(Event::USER_TYPE_SCHEMA_DEFINED)
            ->apply([UserTypeSchema::class, 'whenUserTypeSchemaDefined']);
    }
}