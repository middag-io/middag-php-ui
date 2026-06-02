<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

use Middag\Ui\Block\LayoutElementInterface;

/**
 * Inline field group (e.g. side-by-side fields within a section).
 *
 * Immutable: `fields()` returns a new instance with the given children rather
 * than mutating in place.
 *
 * @internal — construct via the {@see Group::of()} factory
 */
final readonly class Group implements LayoutElementInterface
{
    /**
     * @param array<int, FieldInterface|LayoutElementInterface> $children
     */
    private function __construct(
        private string $id,
        private array $children = [],
    ) {}

    public static function of(string $id): self
    {
        return new self($id);
    }

    public function fields(FieldInterface|LayoutElementInterface ...$items): self
    {
        return new self($this->id, $items);
    }

    public function id(): string
    {
        return $this->id;
    }

    /** @return array<int, FieldInterface|LayoutElementInterface> */
    public function children(): array
    {
        return $this->children;
    }
}
