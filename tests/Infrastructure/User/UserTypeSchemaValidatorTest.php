<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Infrastructure\User;

use App\Api\MsgDesc;
use App\Infrastructure\User\UserTypeSchemaValidator;
use AppTest\BaseTestCase;

class UserTypeSchemaValidatorTest extends BaseTestCase
{
    /**
     * @var UserTypeSchemaValidator
     */
    private $userTypeSchemaValidator;

    protected function setUp()
    {
        parent::setUp();

        $this->eventMachine->bootstrapInTestMode([
            $this->userTypeSchemaDefined()
        ]);

        $this->userTypeSchemaValidator = new UserTypeSchemaValidator($this->eventMachine);
    }

    /**
     * @test
     */
    public function it_validates_user_data_against_type_schema()
    {
        $registerUser = $this->registerUser(self::TYPE_EDITOR, [self::ROLE_EDITOR], [
            'level' => 'member'
        ]);

        $registerUser = $this->userTypeSchemaValidator->preProcess($registerUser);

        $this->assertTrue($registerUser->metadata()[MsgDesc::META_USER_VALIDATED] ?? false);
    }
}
