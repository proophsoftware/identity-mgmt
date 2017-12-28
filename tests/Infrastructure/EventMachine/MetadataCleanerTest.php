<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Infrastructure\EventMachine;

use App\Api\Metadata;
use App\Infrastructure\EventMachine\MetadataCleaner;
use AppTest\BaseTestCase;

class MetadataCleanerTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_cleans_blacklisted_metadata_keys()
    {
        $cmd = $this->registerUser();

        $cmd = $cmd->withAddedMetadata(Metadata::META_PASSWORD_HASHED, true)->withAddedMetadata(Metadata::META_USER_VALIDATED, true);

        $cmd = (new MetadataCleaner())->preProcess($cmd);

        $this->assertEmpty($cmd->metadata());
    }
}