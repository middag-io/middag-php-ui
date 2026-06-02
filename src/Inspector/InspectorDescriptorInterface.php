<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Inspector;

use JsonSerializable;

/**
 * Boundary marker for inspector side-panel descriptors.
 *
 * Carries no methods of its own beyond {@see JsonSerializable}: it exists so host
 * adapters and renderers can type-hint against the contract (an extension seam)
 * rather than the concrete {@see InspectorDescriptor}.
 *
 * @api
 */
interface InspectorDescriptorInterface extends JsonSerializable {}
