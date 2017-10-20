<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest;

use App\Api\MsgDesc;
use App\Model\Identity\Login;
use App\Model\TenantId;
use App\Model\User\UserId;
use PHPUnit\Framework\TestCase;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\Container\EventMachineContainer;
use Prooph\EventMachine\EventMachine;

class BaseTestCase extends TestCase
{
    const TYPE_ADMIN = 'Admin';
    const ROLE_ADMIN = 'Admin';
    const ROLE_EDITOR = 'Editor';

    /**
     * @var EventMachine
     */
    protected $eventMachine;

    /**
     * @var TenantId
     */
    protected $tenantId;

    /**
     * @var UserId
     */
    protected $userId;

    /**
     * @var Login
     */
    protected $adminLogin;

    protected function setUp()
    {
        $this->eventMachine = new EventMachine();

        $config = include __DIR__ . '/../config/autoload/global.php';

        foreach ($config['event_machine']['descriptions'] as $description) {
            $this->eventMachine->load($description);
        }

        $this->eventMachine->initialize(new EventMachineContainer($this->eventMachine));

        $this->tenantId = TenantId::fromString('03c8d742-1bed-46d1-a985-080b9a036656');
        $this->userId = UserId::fromString('42282edf-9007-4218-b3f8-931580d1abd0');
        $this->adminLogin = Login::fromArray([
            'email' => 'admin@prooph.local',
            'passwordHash' => '$2y$10$9s6ahvJfptN/m3BRcl6oJONqvwU0fQeif5KUDuzR259gcOyZqKfRO',
            'verified' => true
        ]);
    }

    protected function tearDown()
    {
        $this->eventMachine = null;
    }

    protected function registerUser(string $type = self::TYPE_ADMIN, array $roles = [self::ROLE_ADMIN]): Message
    {
        return $this->buildCmd(MsgDesc::CMD_REGISTER_USER, [
            MsgDesc::KEY_TENANT_ID => $this->tenantId->toString(),
            MsgDesc::KEY_USER_ID => $this->userId->toString(),
            MsgDesc::KEY_TYPE => $type,
            MsgDesc::KEY_ROLES => $roles,
            MsgDesc::KEY_DATA => ['username' => 'sudo'],
            MsgDesc::KEY_EMAIL => $this->adminLogin->email(),
            MsgDesc::KEY_PASSWORD => 'my_secret',
        ]);
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