<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Form;

use Middag\Ui\Form\Contract\FormSchemaNodeInterface;
use Middag\Ui\Shared\Enum\FormComponent;

/**
 * A single field node in a form schema tree, discriminated by `component`.
 *
 * Wire shape: `{ kind: 'field', key, component, props }`. The `key` is the
 * field identity (the values/errors map key); `props` is the per-component
 * props bag whose shape is constrained by {@see self::jsonSchema()} — a oneOf
 * over the component catalog, mirroring @middag-io/react's `FormFieldNode`
 * union exactly.
 *
 * `props` is kept as an associative array (not a typed sub-VO per component):
 * producers build it as a map and the schema validates it per branch. The base
 * props live in {@see FieldPropsBase}; option fields add {@see OptionFieldProps}.
 *
 * @api
 */
final readonly class FormFieldNode implements FormSchemaNodeInterface
{
    /**
     * @param array<string, mixed> $props
     */
    public function __construct(
        public string $key,
        public FormComponent $component,
        public array $props = [],
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        return [
            'kind' => 'field',
            'key' => $this->key,
            'component' => $this->component->value,
            // Cast so an empty props bag serializes to a JSON object `{}`, never
            // an array `[]` — the renderer always reads props as an object.
            'props' => (object) $this->props,
        ];
    }

    /**
     * Discriminated union on `component`: one closed branch per field
     * interface, mirroring @middag-io/react's `FormFieldNode` union. The
     * component literals partition the FormComponent catalog; a dedicated
     * schema test asserts the partition stays exhaustive and disjoint.
     *
     * @return array<string, mixed>
     */
    public static function jsonSchema(): array
    {
        $str = ['type' => 'string'];
        $int = ['type' => 'integer'];
        $num = ['type' => 'number'];
        $bool = ['type' => 'boolean'];

        return [
            'oneOf' => [
                // text | email | url | password | color — base props only.
                self::branch(['text', 'email', 'url', 'password', 'color'], self::base()),
                // textarea — base + rows.
                self::branch('textarea', self::baseWith(['rows' => $int])),
                // otp — base + segmented-input props.
                self::branch('otp', self::baseWith([
                    'maxLength' => $int,
                    'pattern' => ['enum' => ['digits', 'alphanumeric']],
                    'groupSize' => $int,
                ])),
                // int | float — base + numeric bounds.
                self::branch(['int', 'float'], self::baseWith(['min' => $num, 'max' => $num, 'step' => $num])),
                // slider — base + numeric bounds + range toggle.
                self::branch('slider', self::baseWith([
                    'min' => $num,
                    'max' => $num,
                    'step' => $num,
                    'multiple' => $bool,
                ])),
                // select | radio — option props.
                self::branch(['select', 'radio'], self::option()),
                // multiselect — option props.
                self::branch('multiselect', self::option()),
                // native_select — option props.
                self::branch('native_select', self::option()),
                // checkbox | switch — base props only.
                self::branch(['checkbox', 'switch'], self::base()),
                // date | datetime — base props only.
                self::branch(['date', 'datetime'], self::base()),
                // duration — base props only.
                self::branch('duration', self::base()),
                // file — base + upload constraints.
                self::branch('file', self::baseWith([
                    'accept' => $str,
                    'multiple' => $bool,
                    'maxFiles' => $int,
                    'maxSize' => $int,
                ])),
                // entity_picker — option props + async search props.
                self::branch('entity_picker', self::optionWith([
                    'autocompleteHref' => $str,
                    'autocompleteMinChars' => $int,
                    'entityDisplayField' => $str,
                    'entitySubtitleField' => $str,
                    'entityAvatarField' => $str,
                ])),
                // phone — base + country props.
                self::branch('phone', self::baseWith(['defaultCountry' => $str, 'fixedCountry' => $bool])),
                // document — base + national-document props.
                self::branch('document', self::baseWith([
                    'documentType' => $str,
                    'documentScope' => ['enum' => ['person', 'company', 'any']],
                    'documentMasks' => [
                        'type' => 'object',
                        'additionalProperties' => ['$ref' => '#/$defs/FormFieldDocumentMask'],
                    ],
                ])),
                // currency — base + currency props.
                self::branch('currency', self::baseWith(['currency' => $str, 'currencyLocale' => $str])),
                // slug — base + source/prefix props.
                self::branch('slug', self::baseWith(['sourceField' => $str, 'prefix' => $str])),
                // tags — base + max tags.
                self::branch('tags', self::baseWith(['maxTags' => $int])),
                // rating — base + max rating.
                self::branch('rating', self::baseWith(['maxRating' => $int])),
                // date_range — base + operator preset.
                self::branch('date_range', self::baseWith(['operator' => $str])),
                // hidden — base props only.
                self::branch('hidden', self::base()),
                // static — base props only.
                self::branch('static', self::base()),
                // header — label only (no base props).
                self::branch('header', [
                    'type' => 'object',
                    'required' => ['label'],
                    'properties' => ['label' => $str],
                    'additionalProperties' => false,
                ]),
            ],
        ];
    }

    /**
     * Build one closed field branch.
     *
     * @param list<string>|string  $component literal(s) for the `component` discriminant
     * @param array<string, mixed> $props     schema for the branch's `props`
     *
     * @return array<string, mixed>
     */
    private static function branch(array|string $component, array $props): array
    {
        return [
            'type' => 'object',
            'required' => ['kind', 'key', 'component', 'props'],
            'properties' => [
                'kind' => ['const' => 'field'],
                'key' => ['type' => 'string'],
                'component' => is_array($component) ? ['enum' => $component] : ['const' => $component],
                'props' => $props,
            ],
            'additionalProperties' => false,
        ];
    }

    /** @return array<string, mixed> */
    private static function base(): array
    {
        return ['$ref' => '#/$defs/FieldPropsBase'];
    }

    /**
     * Base props intersected with component-specific props (kept open under
     * `allOf` — see {@see FieldPropsBase} on the additionalProperties pitfall).
     *
     * @param array<string, mixed> $extra
     *
     * @return array<string, mixed>
     */
    private static function baseWith(array $extra): array
    {
        return ['allOf' => [['$ref' => '#/$defs/FieldPropsBase'], ['type' => 'object', 'properties' => $extra]]];
    }

    /** @return array<string, mixed> */
    private static function option(): array
    {
        return ['$ref' => '#/$defs/OptionFieldProps'];
    }

    /**
     * Option props intersected with component-specific props.
     *
     * @param array<string, mixed> $extra
     *
     * @return array<string, mixed>
     */
    private static function optionWith(array $extra): array
    {
        return ['allOf' => [['$ref' => '#/$defs/OptionFieldProps'], ['type' => 'object', 'properties' => $extra]]];
    }
}
