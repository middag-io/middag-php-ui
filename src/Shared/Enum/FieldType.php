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
 * Closed catalog of MVP form field types.
 *
 * Adding a value requires: ADR amendment + matching field class +
 * a renderer-mapper case per host adapter + a client component.
 *
 * @api
 */
enum FieldType: string
{
    use ProvidesJsonSchema;

    case Text = 'text';

    case Textarea = 'textarea';

    case Richtext = 'richtext';

    case Password = 'password';

    case Otp = 'otp';

    case Email = 'email';

    case Url = 'url';

    case Int = 'int';

    case Float = 'float';

    case Slider = 'slider';

    case Select = 'select';

    case Multiselect = 'multiselect';

    case NativeSelect = 'native_select';

    case Radio = 'radio';

    case Checkbox = 'checkbox';

    case Switch = 'switch';

    case Date = 'date';

    case Datetime = 'datetime';

    case Time = 'time';

    case Duration = 'duration';

    case File = 'file';

    case EntityPicker = 'entity_picker';

    case Autocomplete = 'autocomplete';

    case Tags = 'tags';

    case Hidden = 'hidden';

    case Static = 'static';

    case Header = 'header';
}
