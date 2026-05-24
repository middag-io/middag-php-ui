<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Enum;

/**
 * Identity token for form renderer adapters (ADR-805).
 *
 * Declares which rendering target a form renderer adapter serves.
 * Used by the form model to select the correct adapter at render time.
 *
 * @api
 */
enum RenderTarget: string
{
    case MFORM = 'mform';

    case INERTIA = 'inertia';
}
