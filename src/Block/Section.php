<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Block;

use Middag\Ui\Block\Contract\LayoutElementInterface;
use Middag\Ui\Form\Contract\FieldInterface;
use Middag\Ui\Shared\Data\Label;
use Middag\Ui\Shared\Data\Translatable;

/**
 * Top-level form layout section grouping fields and nested elements.
 *
 * Immutable: `label()` and `fields()` return a new instance rather than
 * mutating in place.
 *
 * @internal — construct via the {@see Section::of()} named constructor
 */
final readonly class Section implements LayoutElementInterface
{
    /**
     * @param array<int, FieldInterface|LayoutElementInterface> $children
     */
    private function __construct(
        private string $id,
        private string|Translatable|null $label = null,
        private array $children = [],
    ) {}

    public static function of(string $id): self
    {
        return new self($id);
    }

    public function label(string|Translatable $label): self
    {
        return new self($this->id, $label, $this->children);
    }

    public function fields(FieldInterface|LayoutElementInterface ...$items): self
    {
        return new self($this->id, $this->label, $items);
    }

    public function id(): string
    {
        return $this->id;
    }

    /**
     * The serialized label: a Translatable's `{key, domain, params?}` payload,
     * a raw literal string, or null when unset. Aligned with every other VO
     * label via {@see Label::serialize}.
     *
     * @return null|array<string, mixed>|string
     */
    public function labelData(): array|string|null
    {
        return Label::serializeNullable($this->label);
    }

    /** @return array<int, FieldInterface|LayoutElementInterface> */
    public function children(): array
    {
        return $this->children;
    }
}
