<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Infrastructure\Form\Layout;

use Middag\Ui\Contract\Form\FieldInterface as field_interface;
use Middag\Ui\Contract\Form\LayoutElementInterface as layout_element_interface;

/**
 * Top-level form layout section grouping fields and nested elements.
 *
 * @internal — use base/form/section factory
 */
final class Section implements layout_element_interface
{
    /** @var null|array{key: string, component: string} */
    private ?array $label = null;

    /** @var array<int, field_interface|layout_element_interface> */
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

    public function fields(field_interface|layout_element_interface ...$items): self
    {
        $this->children = $items;

        return $this;
    }

    public function id(): string
    {
        return $this->id;
    }

    /** @return null|array{key: string, component: string} */
    public function label_data(): ?array
    {
        return $this->label;
    }

    /** @return array<int, field_interface|layout_element_interface> */
    public function children(): array
    {
        return $this->children;
    }
}
