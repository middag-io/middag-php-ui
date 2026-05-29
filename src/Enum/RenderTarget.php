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

use Middag\Ui\Enum\Concerns\ProvidesJsonSchema;

/**
 * Identity token for form renderer adapters (ADR-805).
 *
 * Declares the output MEDIUM a renderer adapter produces — a rendered HTML
 * body, or a structured props payload — not a concrete transport. Used by the
 * form model to select the correct adapter at render time.
 *
 * @api
 */
enum RenderTarget: string
{
    use ProvidesJsonSchema;

    case HTML = 'html';

    case PROPS = 'props';
}
