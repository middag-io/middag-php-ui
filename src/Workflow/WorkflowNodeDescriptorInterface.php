<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Workflow;

/**
 * Descriptor for a workflow node type in the visual editor.
 *
 * Extensions implement this interface to register custom node types (triggers,
 * actions, logic) along with the config UI schema for each type. The visual
 * editor renders the config form dynamically from {@see getConfigSchema()},
 * so no form is hardcoded per node type. How descriptors are registered and
 * discovered is the host adapter's concern and is not part of this contract.
 *
 * @api
 */
interface WorkflowNodeDescriptorInterface
{
    /**
     * Returns the node category: 'trigger', 'action', or 'logic'.
     */
    public function getCategory(): string;

    /**
     * Returns the node type identifier within its category.
     */
    public function getNodeType(): string;

    /**
     * Returns human-readable label for the palette.
     */
    public function getLabel(): string;

    /**
     * Returns icon identifier for the palette (client icon-set name).
     */
    public function getIcon(): string;

    /**
     * Returns configurable fields for the node config panel.
     *
     * @return list<array{key: string, label: string, type: string, required: bool, options?: list<array{value: string, label: string}>, placeholder?: string, help?: string}>
     */
    public function getConfigSchema(): array;
}
