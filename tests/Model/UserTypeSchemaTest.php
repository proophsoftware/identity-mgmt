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
use App\Model\UserTypeSchema\UserType;
use App\Model\UserTypeSchema\UserTypeId;
use AppTest\BaseTestCase;
use Prooph\Common\Messaging\Message;

class UserTypeSchemaTest extends BaseTestCase
{
    private $adminSchema = [
        'type' => 'object',
        'properties' => [
            'username' => ['type' => 'string', 'minLength' => 1],
        ],
    ];

    /**
     * @test
     */
    public function it_can_be_defined_with_a_schema()
    {
        $this->eventMachine->bootstrapInTestMode([], $this->getDefineUserTypeSchemaServices());

        $payload = [
            Payload::KEY_TENANT_ID => $this->tenantId->toString(),
            Payload::KEY_TYPE => 'admin',
            Payload::KEY_SCHEMA => $this->adminSchema,
        ];

        $defineSchema = $this->buildCmd(Command::DEFINE_USER_TYPE_SCHEMA, $payload);

        $this->eventMachine->dispatch($defineSchema);

        /** @var Message[] $events */
        $events = $this->eventMachine->popRecordedEventsOfTestSession();

        $payload[Payload::KEY_TYPE_ID] = UserTypeId::fromValues(
            $this->tenantId,
            UserType::fromString('admin')
        )->toString();

        $this->assertCount(1, $events);
        $this->assertSame(Event::USER_TYPE_SCHEMA_DEFINED, $events[0]->messageName());
        $this->assertEquals($payload, $events[0]->payload());
    }
}