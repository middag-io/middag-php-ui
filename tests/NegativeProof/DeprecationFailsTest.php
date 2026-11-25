<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\NegativeProof;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Negative proof: a deprecation must fail the suite. Never merge.
 *
 * @internal
 */
#[CoversNothing]
final class DeprecationFailsTest extends TestCase
{
    #[Test]
    public function aDeprecationFailsTheSuite(): void
    {
        trigger_error('negative proof', E_USER_DEPRECATED);
        $this->assertTrue(true);
    }
}
