<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\Enum;

use Middag\Ui\Form\FormFieldNode;
use Middag\Ui\Shared\ProvidesJsonSchema;

/**
 * Wire catalog of renderable form field components — the discriminant of a
 * {@see FormFieldNode} on the wire.
 *
 * Mirrors @middag-io/react's `FormFieldType`: it is the renderer's component
 * catalog, NOT the builder catalog. It is deliberately distinct from
 * {@see FieldType} (the MVP builder catalog used by FieldDefinition): the wire
 * catalog adds renderer-only components (phone, document, currency, color,
 * slug, rating, date_range) and omits builder-only intents (richtext, time,
 * autocomplete) that map onto these components host-side.
 *
 * @api
 */
enum FormComponent: string
{
    use ProvidesJsonSchema;

    case TEXT = 'text';

    case TEXTAREA = 'textarea';

    case PASSWORD = 'password';

    case OTP = 'otp';

    case EMAIL = 'email';

    case URL = 'url';

    case INT = 'int';

    case FLOAT = 'float';

    case SLIDER = 'slider';

    case SELECT = 'select';

    case NATIVE_SELECT = 'native_select';

    case MULTISELECT = 'multiselect';

    case RADIO = 'radio';

    case CHECKBOX = 'checkbox';

    case SWITCH = 'switch';

    case DATE = 'date';

    case DATETIME = 'datetime';

    case DURATION = 'duration';

    case FILE = 'file';

    case ENTITY_PICKER = 'entity_picker';

    case PHONE = 'phone';

    case DOCUMENT = 'document';

    case CURRENCY = 'currency';

    case COLOR = 'color';

    case SLUG = 'slug';

    case TAGS = 'tags';

    case RATING = 'rating';

    case DATE_RANGE = 'date_range';

    case HIDDEN = 'hidden';

    case STATIC = 'static';

    case HEADER = 'header';
}
