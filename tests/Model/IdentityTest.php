<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Model;

use App\Api\Command;
use App\Api\Event;
use App\Api\Payload;
use App\Api\PayloadFactory;
use App\Model\Identity\Verification;
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
        $this->assertEquals(Event::IDENTITY_ADDED, $events[0]->messageName());
    }

    /**
     * @test
     */
    public function it_verifies_identity()
    {
        $identityAdded = $this->identityAdded();
        $this->eventMachine->bootstrapInTestMode([
            $identityAdded
        ]);

        $this->eventMachine->dispatch(Command::VERIFY_IDENTITY, PayloadFactory::makeVerifyIdentityPayload(
            $this->adminIdentity->identityId()->toString(),
            Verification::fromArray($identityAdded->payload()[Payload::KEY_VERIFICATION])->verificationId()->toString()
        ));

        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertEquals(Event::IDENTITY_VERIFIED, $events[0]->messageName());
    }
}
