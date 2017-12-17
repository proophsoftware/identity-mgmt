<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppTest\Infrastructure;

use function App\Infrastructure\combine_regex_patterns;
use AppTest\BaseTestCase;
use Ramsey\Uuid\Uuid;

class UtilTest extends BaseTestCase
{
    /**
     * @test
     */
    public function it_combines_regex_patterns()
    {
        $patternA = '^[\w]+$';
        $patternB = '^:::$';
        $patternC = Uuid::VALID_PATTERN;

        $combinedPattern = combine_regex_patterns($patternA, $patternB, $patternC);

        $this->assertSame('^[\w]+:::[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$', $combinedPattern);
    }
}