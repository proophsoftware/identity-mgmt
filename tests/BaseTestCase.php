<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest;

use App\Model\TenantId;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\EventMachine;

class BaseTestCase extends TestCase
{
    /**
     * @var EventMachine
     */
    protected $eventMachine;

    /**
     * @var TenantId
     */
    protected $tenantId;

    protected function setUp()
    {
        $this->eventMachine = new EventMachine();

        $config = include __DIR__ . '/../config/autoload/global.php';

        foreach ($config['event_machine']['descriptions'] as $description) {
            $this->eventMachine->load($description);
        }

        $this->eventMachine->initialize(new EventMachineContainer($this->eventMachine));

        $this->tenantId = TenantId::fromString('03c8d742-1bed-46d1-a985-080b9a036656');
    }

    protected function tearDown()
    {
        $this->eventMachine = null;
    }

    protected function buildCmd(string $cmdName, array $payload, array $metadata = []): Message
    {
        return $this->buildMessage($cmdName, $payload, $metadata);
    }

    protected function buildEvent(string $evtName, array $payload, array $metadata = []): Message
    {
        return $this->buildMessage($evtName, $payload, $metadata);
    }

    protected function buildMessage(string $msgName, array $payload, array $metadata = []): Message
    {
        return $this->eventMachine->messageFactory()->createMessageFromArray($msgName, [
            'payload' => $payload,
            'metadata' => $metadata
        ]);
    }
}