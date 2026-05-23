<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract;

/**
 * Descriptor for workflow node types in the visual editor (CC-03).
 *
 * Extensions implement this interface to register custom action/trigger types
 * with their config UI schema. The visual editor renders config forms dynamically
 * from get_config_schema() — no hardcoded forms per type.
 *
 * Registration: extensions call workflow_registry::register_descriptor() in boot().
 * Discovery: workflow_api_controller aggregates all descriptors for the node palette.
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
     * Returns the node type identifier (matches action_type or trigger_type).
     */
    public function getNodeType(): string;

    /**
     * Returns human-readable label for the palette.
     */
    public function getLabel(): string;

    /**
     * Returns icon identifier for the palette (Lucide icon name).
     */
    public function getIcon(): string;

    /**
     * Returns configurable fields for the NodeConfigPanel.
     *
     * @return list<array{key: string, label: string, type: string, required: bool, options?: list<array{value: string, label: string}>, placeholder?: string, help?: string}>
     */
    public function getConfigSchema(): array;
}
