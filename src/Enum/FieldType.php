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
 * Closed catalog of MVP form field types (ADR-806).
 *
 * Adding a value requires: ADR amendment + matching field class +
 * a renderer-mapper case per host adapter + a client component.
 *
 * @api
 */
enum FieldType: string
{
    use ProvidesJsonSchema;

    case TEXT = 'text';

    case TEXTAREA = 'textarea';

    case RICHTEXT = 'richtext';

    case PASSWORD = 'password';

    case EMAIL = 'email';

    case URL = 'url';

    case INT = 'int';

    case FLOAT = 'float';

    case SELECT = 'select';

    case MULTISELECT = 'multiselect';

    case RADIO = 'radio';

    case CHECKBOX = 'checkbox';

    case SWITCH = 'switch';

    case DATE = 'date';

    case DATETIME = 'datetime';

    case TIME = 'time';

    case DURATION = 'duration';

    case FILE = 'file';

    case ENTITY_PICKER = 'entity_picker';

    case AUTOCOMPLETE = 'autocomplete';

    case TAGS = 'tags';

    case HIDDEN = 'hidden';

    case STATIC = 'static';

    case HEADER = 'header';
}
