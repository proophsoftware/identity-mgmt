<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Infrastructure\VerificationSession;

use App\Api\Event;
use App\Infrastructure\VerificationSession\StartVerificationSession;
use AppTest\BaseTestCase;

class StartVerificationSessionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function if_identity_added_then_start_verfication_session()
    {
        $this->eventMachine->bootstrapInTestMode([], [
            StartVerificationSession::class => new StartVerificationSession($this->eventMachine)
        ]);

        $this->eventMachine->dispatch($this->identityAdded());

        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertEquals(Event::VERIFICATION_SESSION_STARTED, $events[0]->messageName());
    }
}
