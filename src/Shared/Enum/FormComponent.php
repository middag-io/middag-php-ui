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

use Middag\Ui\Shared\Concerns\ProvidesJsonSchema;

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

    case Text = 'text';

    case Textarea = 'textarea';

    case Password = 'password';

    case Otp = 'otp';

    case Email = 'email';

    case Url = 'url';

    case Int = 'int';

    case Float = 'float';

    case Slider = 'slider';

    case Select = 'select';

    case NativeSelect = 'native_select';

    case Multiselect = 'multiselect';

    case Radio = 'radio';

    case Checkbox = 'checkbox';

    case Switch = 'switch';

    case Date = 'date';

    case Datetime = 'datetime';

    case Duration = 'duration';

    case File = 'file';

    case EntityPicker = 'entity_picker';

    case Phone = 'phone';

    case Document = 'document';

    case Currency = 'currency';

    case Color = 'color';

    case Slug = 'slug';

    case Tags = 'tags';

    case Rating = 'rating';

    case DateRange = 'date_range';

    case Hidden = 'hidden';

    case Static = 'static';

    case Header = 'header';
}
