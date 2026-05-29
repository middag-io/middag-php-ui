<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Data\Form\Layout;

use Middag\Ui\Contract\Form\FieldInterface;
use Middag\Ui\Contract\Form\LayoutElementInterface;

/**
 * Inline field group (e.g. side-by-side fields within a section).
 *
 * @internal — use base/form/group factory
 */
final class Group implements LayoutElementInterface
{
    /** @var array<int, FieldInterface|LayoutElementInterface> */
    private array $children = [];

    private function __construct(private readonly string $id) {}

    public static function of(string $id): self
    {
        return new self($id);
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

    /** @return array<int, FieldInterface|LayoutElementInterface> */
    public function children(): array
    {
        return $this->children;
    }
}
