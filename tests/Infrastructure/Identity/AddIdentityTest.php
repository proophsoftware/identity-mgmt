<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Infrastructure\Identity;

use App\Api\MsgDesc;
use App\Infrastructure\Identity\AddIdentity;
use AppTest\BaseTestCase;

class AddIdentityTest extends BaseTestCase
{
    /**
     * @test
     */
    public function if_user_registered_than_add_identity()
    {
        $this->eventMachine->bootstrapInTestMode([], [
            AddIdentity::class => new AddIdentity($this->eventMachine)
        ]);

        $this->eventMachine->dispatch($this->userRegistered());

        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertEquals(MsgDesc::EVT_IDENTITY_ADDED, $events[0]->messageName());
    }
}
