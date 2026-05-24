<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Infrastructure\Form\Layout;

use Middag\Ui\Contract\Form\FieldInterface;
use Middag\Ui\Contract\Form\LayoutElementInterface;

/**
 * Top-level form layout section grouping fields and nested elements.
 *
 * @internal — use base/form/section factory
 */
final class Section implements LayoutElementInterface
{
    /** @var null|array{key: string, component: string} */
    private ?array $label = null;

    /** @var array<int, FieldInterface|LayoutElementInterface> */
    private array $children = [];

    private function __construct(private readonly string $id) {}

    public static function of(string $id): self
    {
        return new self($id);
    }

    public function label(string $key, string $component = ''): self
    {
        $this->label = ['key' => $key, 'component' => $component];

        return $this;
    }

    public function fields(FieldInterface|LayoutElementInterface ...$items): self
    {
        $this->children = $items;

        return $this;
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
