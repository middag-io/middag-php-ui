<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Navigation;

use InvalidArgumentException;
use JsonSerializable;

/**
 * The navigation payload shared with the client: the main tree, the footer
 * list and the key of the active node.
 *
 * Every key is always emitted. `activeKey` is the empty string when no node is
 * active.
 *
 * @api
 */
final readonly class NavigationTree implements JsonSerializable
{
    /**
     * @param list<NavigationNode> $tree
     * @param string               $activeKey Dot-notation key of the active node, '' when none
     * @param list<NavigationNode> $footer
     *
     * @throws InvalidArgumentException when `tree` or `footer` is not a list of NavigationNode
     */
    public function __construct(
        public array $tree = [],
        public string $activeKey = '',
        public array $footer = [],
    ) {
        $this->assertNodeList($tree, 'tree');
        $this->assertNodeList($footer, 'footer');
    }

    /** @return array{tree: list<array<string, mixed>>, activeKey: string, footer: list<array<string, mixed>>} */
    public function jsonSerialize(): array
    {
        return [
            'tree' => array_map(static fn (NavigationNode $node): array => $node->jsonSerialize(), $this->tree),
            'activeKey' => $this->activeKey,
            'footer' => array_map(static fn (NavigationNode $node): array => $node->jsonSerialize(), $this->footer),
        ];
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object', 'required' => ['tree', 'activeKey', 'footer'],
            'properties' => [
                'tree' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/NavigationNode']],
                'activeKey' => ['type' => 'string'],
                'footer' => ['type' => 'array', 'items' => ['$ref' => '#/$defs/NavigationNode']],
            ],
            'additionalProperties' => false];
    }

    /**
     * @param array<array-key, mixed> $nodes
     */
    private function assertNodeList(array $nodes, string $field): void
    {
        if (!array_is_list($nodes)) {
            throw new InvalidArgumentException(sprintf('NavigationTree::$%s must be a list.', $field));
        }

        foreach ($nodes as $node) {
            if (!$node instanceof NavigationNode) {
                throw new InvalidArgumentException(sprintf(
                    'NavigationTree::$%s must contain only %s, %s given.',
                    $field,
                    NavigationNode::class,
                    get_debug_type($node),
                ));
            }
        }
    }
}
