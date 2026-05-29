<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data;

use Middag\Ui\Contract\FieldInterface;
use Middag\Ui\Contract\LayoutElementInterface;

/**
 * Top-level form layout section grouping fields and nested elements.
 *
 * Immutable: `label()` and `fields()` return a new instance rather than
 * mutating in place.
 *
 * @internal — use base/form/section factory
 */
final readonly class Section implements LayoutElementInterface
{
    /**
     * @param null|array{key: string, component: string}        $label
     * @param array<int, FieldInterface|LayoutElementInterface> $children
     */
    private function __construct(
        private string $id,
        private ?array $label = null,
        private array $children = [],
    ) {}

    public static function of(string $id): self
    {
        return new self($id);
    }

    public function label(string $key, string $component = ''): self
    {
        return new self($this->id, ['key' => $key, 'component' => $component], $this->children);
    }

    public function fields(FieldInterface|LayoutElementInterface ...$items): self
    {
        return new self($this->id, $this->label, $items);
    }

    public function id(): string
    {
        return $this->id;
    }

    /** @return null|array{key: string, component: string} */
    public function labelData(): ?array
    {
        return $this->label;
    }

    /** @return array<int, FieldInterface|LayoutElementInterface> */
    public function children(): array
    {
        return $this->children;
    }
}
