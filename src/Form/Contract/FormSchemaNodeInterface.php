<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form\Contract;

use JsonSerializable;
use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Form\FormGroupNode;
use Middag\Ui\Form\FormHeaderNode;
use Middag\Ui\Form\FormSchemaNode;
use Middag\Ui\Form\FormSectionNode;

/**
 * A node in a form schema tree on the wire: a field, section, group or header.
 *
 * Implemented by the serializable wire VOs ({@see FormFieldNode},
 * {@see FormSectionNode}, {@see FormGroupNode},
 * {@see FormHeaderNode}) so a section/group can type its
 * `children` as a heterogeneous list of nodes. The matching schema union is the
 * `FormSchemaNode` $def ({@see FormSchemaNode}).
 *
 * @api
 */
interface FormSchemaNodeInterface extends JsonSerializable {}
