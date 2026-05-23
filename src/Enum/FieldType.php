<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Enum;

/**
 * Closed catalog of MVP form field types (ADR-806).
 *
 * Adding a value requires: ADR amendment + matching field class +
 * mform_field_mapper case + inertia_field_mapper case + Vue component.
 *
 * @api
 */
enum FieldType: string
{
    case TEXT = 'text';

    case TEXTAREA = 'textarea';

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

    case DURATION = 'duration';

    case FILE = 'file';

    case ENTITY_PICKER = 'entity_picker';

    case HIDDEN = 'hidden';

    case STATIC = 'static';

    case HEADER = 'header';
}
