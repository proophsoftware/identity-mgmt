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
use function App\Infrastructure\Password\pwd_verify;
use AppTest\BaseTestCase;
use Prooph\Common\Messaging\Message;

class UserTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_is_registered_and_password_is_hashed()
    {
        $this->eventMachine->bootstrapInTestMode([], $this->getRegisterUserServices(false));

        $this->eventMachine->dispatch($this->registerUser());

        /** @var Message[] $events */
        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $this->assertCount(1, $events);
        $this->assertSame(MsgDesc::EVT_USER_REGISTERED, $events[0]->messageName());

        $cmdPayload = $this->registerUser()->payload();
        $evtPayload = $events[0]->payload();

        $pwdHash = $evtPayload[MsgDesc::KEY_PASSWORD];

        //Prepare for comparision
        unset($evtPayload[MsgDesc::KEY_PASSWORD]);
        unset($cmdPayload[MsgDesc::KEY_PASSWORD]);

        $this->assertEquals($cmdPayload, $evtPayload);
        $this->assertTrue(pwd_verify('my_secret', $pwdHash));
    }
}
