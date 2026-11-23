<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Shared\ValueObject;

use JsonSerializable;
use Middag\Ui\Form\FormErrorBags;
use Middag\Ui\Form\FormErrors;
use Middag\Ui\Navigation\NavigationTree;

/**
 * Root of the shared props every page receives next to the page contract.
 *
 * Each slot is optional and emitted only when set; `scope` is always emitted
 * and serializes to `{}` when empty. The root is open (`additionalProperties`
 * is true) so hosts and products can add their own keys, such as `middag.*`.
 * This class describes the typed slots for the schema bundle.
 *
 * The `errors` slot carries the validation errors of the current request: a
 * {@see FormErrors} map or, when the request carried the `X-Inertia-Error-Bag`
 * header, a {@see FormErrorBags} map of bag name to FormErrors. The two shapes
 * can overlap on the wire, so the wire does not say which one it is: a client
 * that sent the header reads `errors[bag]`, and every other client reads the
 * map as flat. On the PHP side the type is unambiguous.
 *
 * @api
 */
final readonly class SharedProps implements JsonSerializable
{
    /**
     * @param array<string, mixed> $scope Open object describing the current scope
     */
    public function __construct(
        public ?SharedAuth $auth = null,
        public ?SharedTheme $theme = null,
        public ?FlashMessages $flash = null,
        public FormErrorBags|FormErrors|null $errors = null,
        public ?string $csrfToken = null,
        public ?string $locale = null,
        public array $scope = [],
        public ?NavigationTree $navigation = null,
    ) {}

    /** @return array<string, mixed> */
    public function jsonSerialize(): array
    {
        $payload = [];

        if ($this->auth instanceof SharedAuth) {
            $payload['auth'] = $this->auth->jsonSerialize();
        }

        if ($this->theme instanceof SharedTheme) {
            $payload['theme'] = $this->theme->jsonSerialize();
        }

        if ($this->flash instanceof FlashMessages) {
            $payload['flash'] = $this->flash->jsonSerialize();
        }

        if ($this->errors !== null) {
            $payload['errors'] = $this->errors->jsonSerialize();
        }

        if ($this->csrfToken !== null) {
            $payload['csrf_token'] = $this->csrfToken;
        }

        if ($this->locale !== null) {
            $payload['locale'] = $this->locale;
        }

        $payload['scope'] = (object) $this->scope;

        if ($this->navigation instanceof NavigationTree) {
            $payload['navigation'] = $this->navigation->jsonSerialize();
        }

        return $payload;
    }

    /** @return array<string, mixed> */
    public static function jsonSchema(): array
    {
        return ['type' => 'object',
            'properties' => [
                'auth' => ['$ref' => '#/$defs/SharedAuth'],
                'theme' => ['$ref' => '#/$defs/SharedTheme'],
                'flash' => ['$ref' => '#/$defs/FlashMessages'],
                'errors' => [
                    'description' => 'Validation errors of the current request: a FormErrors map or, when the request carried the X-Inertia-Error-Bag header, a FormErrorBags map of bag name to FormErrors. The wire is not self-describing: the reader that sent the header reads errors[bag]; any other reader treats the map as flat.',
                    'anyOf' => [['$ref' => '#/$defs/FormErrors'], ['$ref' => '#/$defs/FormErrorBags']],
                ],
                'csrf_token' => ['type' => 'string'],
                'locale' => ['type' => 'string'],
                'scope' => ['type' => 'object', 'additionalProperties' => true],
                'navigation' => ['$ref' => '#/$defs/NavigationTree'],
            ],
            'additionalProperties' => true];
    }
}
