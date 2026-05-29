# Changelog

## [1.0.0](https://github.com/middag-io/middag-php-ui/compare/v0.4.0...v1.0.0) (2026-05-29)


### ⚠ BREAKING CHANGES

* **ui:** BlockBuilder::activityTimeline() / markdownPanel() parameters and their wire keys (hasMore, loadMoreHref, maxHeight) are renamed; PageBuilder::help() parameter learn_more -> learnMore. Downstream reconciled in cascade.

### Features

* **ui:** add partial fragment contracts for client-owned pages (Fase 3) ([eb86e94](https://github.com/middag-io/middag-php-ui/commit/eb86e94b6a3bb2e1047a30b4a2ee13f83125b445))
* **ui:** close contract gaps for class-admin parity (i18n, prefs, tables, actions) ([a6b9288](https://github.com/middag-io/middag-php-ui/commit/a6b92889cffc282ebd445a55586479ebf3464c2b))
* **ui:** CrudBuilder filter controls and searchable index ([39e4ac1](https://github.com/middag-io/middag-php-ui/commit/39e4ac1b932f4277d3cc1f5ee5f87271e529e37f))
* **ui:** typed Tab and ChartSeries block VOs ([d996d0b](https://github.com/middag-io/middag-php-ui/commit/d996d0b4f432373ff3ccdb753cd2480aa68b4fdf))
* **ui:** wire CrudBuilder capability; remove dead form() ([8bd16b7](https://github.com/middag-io/middag-php-ui/commit/8bd16b7a6d27a250c479c019226d2da8611ad94a))


### Bug Fixes

* **ci:** php-cs-fixer config parse error on PHP 8.2/8.3 ([db9ab3b](https://github.com/middag-io/middag-php-ui/commit/db9ab3b52aa542b1c8258f9fca5cede79ad9702e))
* **ui:** honest CrudBuilder naming + i18n titles; FormStep fields string-only ([f98ccb3](https://github.com/middag-io/middag-php-ui/commit/f98ccb35f195ec6379792f6bf34d659c1f4aeab9))


### Code Refactoring

* **ui:** camelCase the last snake_case wire keys and params ([3df91af](https://github.com/middag-io/middag-php-ui/commit/3df91af568e2c699c9bb709fb5eb21f9f88d3629))

## [0.4.0](https://github.com/middag-io/middag-php-ui/compare/v0.3.0...v0.4.0) (2026-05-26)


### Features

* **page:** port PageContract impl from local_middag ([#5](https://github.com/middag-io/middag-php-ui/issues/5)) ([66bca6b](https://github.com/middag-io/middag-php-ui/commit/66bca6b1c3ccb601c9fb790934a8d224ba6382cf))

## [0.3.0](https://github.com/middag-io/middag-php-ui/compare/v0.2.0...v0.3.0) (2026-05-24)


### Miscellaneous Chores

* release 0.3.0 ([68f10d9](https://github.com/middag-io/middag-php-ui/commit/68f10d9750c56626f978e85aad46d290413e50c9))

## [0.2.0](https://github.com/middag-io/middag-php-ui/compare/v0.1.0...v0.2.0) (2026-05-23)


### Features

* **contract:** host Form contracts + Shared/Form data + Form enums (B-002, PD-039 A) ([6317195](https://github.com/middag-io/middag-php-ui/commit/63171950719f9b9aee63da5823573d2ef3e1e0ac))
* **contracts:** add PageInterface marker (B-009) ([c3c2c6b](https://github.com/middag-io/middag-php-ui/commit/c3c2c6bff029155d6d36dcd9646de759ad9da208))
* **contracts:** port 5 UI contracts from framework (B-001/B-003) ([7d3320e](https://github.com/middag-io/middag-php-ui/commit/7d3320e081b0c9147c7f81d42cf189d360c09e3e))
* **infrastructure:** host Inertia form renderer + Layout primitives (B-017) ([30f5de6](https://github.com/middag-io/middag-php-ui/commit/30f5de69140ee6a441911ca29f62bea8056acc4a))
* **page:** add AbstractPage base class (B-039) ([c7b56fd](https://github.com/middag-io/middag-php-ui/commit/c7b56fd767e56b1ac55f911c63023a0665ef4e58))
* **ui:** add Support/ utilities (CrudConventionResolver, CrudControllerSupport) ([ee1eee1](https://github.com/middag-io/middag-php-ui/commit/ee1eee1c032b59c45b3b79eea36326ba2bcc4b36))
* **ui:** port Widget triple + Table DTOs from framework/moodle (B-040/B-041) ([0d1143a](https://github.com/middag-io/middag-php-ui/commit/0d1143a81babb68b28164ea8631e4c8e83c4e41c))
