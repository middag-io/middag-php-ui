<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Contract;

/**
 * Marker interface for page classes resolved by the container.
 *
 * The kernel resolves `{slug}_page` services by tag: any class implementing
 * PageInterface is discoverable as a routable page. Implementations typically
 * produce a PageContractInterface via a PageBuilder, but the marker carries
 * no methods — it exists solely for service discovery and type-tagging.
 *
 * Extensions register page classes during boot(); controllers and the
 * router resolve them by slug → FQN via the container.
 *
 * @api
 */
interface PageInterface {}
