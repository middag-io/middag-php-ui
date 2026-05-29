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

use JsonSerializable;

/**
 * Shared base for every contract envelope on the wire.
 *
 * Both the full page envelope (PageContract) and the partial envelope
 * (Fragment) implement this. The single VERSION constant lives here so full
 * and partial payloads can never drift apart; each serialized envelope carries
 * it as `version` for the client to route on.
 *
 * @api
 */
interface ContractEnvelopeInterface extends JsonSerializable
{
    public const VERSION = '1';
}
