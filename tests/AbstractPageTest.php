<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests;

use LogicException;
use Middag\Ui\AbstractPage;
use Middag\Ui\Contract\PageContractInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractPage::class)]
final class AbstractPageTest extends TestCase
{
    #[Test]
    public function testSlugReturnsSubclassConstant(): void
    {
        $page = new class extends AbstractPage {
            public const SLUG = 'dashboard';
        };

        self::assertSame('dashboard', $page->slug());
    }

    #[Test]
    public function testBuildThrowsByDefaultForMultiMethodPages(): void
    {
        $page = new class extends AbstractPage {
            public const SLUG = 'reports';
        };

        $this->expectException(LogicException::class);

        $page->build();
    }

    #[Test]
    public function testBuildCanBeOverriddenBySinglePage(): void
    {
        $page = new class extends AbstractPage {
            public const SLUG = 'single';

            public function build(): PageContractInterface
            {
                return new class implements PageContractInterface {
                    /** @return array<string, mixed> */
                    public function jsonSerialize(): array
                    {
                        return ['ok' => true];
                    }
                };
            }
        };

        self::assertSame(['ok' => true], $page->build()->jsonSerialize());
    }
}
