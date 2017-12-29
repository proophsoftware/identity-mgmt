<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Model;

use App\Api\Event;
use App\Api\Payload;
use function App\Infrastructure\now;
use App\Model\VerificationSession\SessionExpiration;
use AppTest\BaseTestCase;

final class VerificationSessionTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_starts_verification_session_which_expires_in_60_minutes()
    {
        $this->eventMachine->bootstrapInTestMode([]);

        $this->eventMachine->dispatch($this->startVerificationSession());

        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertEquals(Event::VERIFICATION_SESSION_STARTED, $events[0]->messageName());

        $sessionExpiration = SessionExpiration::fromString($events[0]->payload()[Payload::KEY_VERIFICATION_SESSION_EXPIRATION]);

        $in59Minutes = now()->add(new \DateInterval('PT59M'));
        $in61Minutes = now()->add(new \DateInterval('PT1H1M'));

        $this->assertFalse($sessionExpiration->isExpired($in59Minutes));
        $this->assertTrue($sessionExpiration->isExpired($in61Minutes));
    }
}
