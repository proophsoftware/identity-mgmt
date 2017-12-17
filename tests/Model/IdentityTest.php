<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Model;

use App\Api\MsgDesc;
use AppTest\BaseTestCase;

class IdentityTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_adds_identity()
    {
        $this->eventMachine->bootstrapInTestMode([]);

        $this->eventMachine->dispatch($this->addIdentity());

        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertEquals(MsgDesc::EVT_IDENTITY_ADDED, $events[0]->messageName());
    }
}
