# Changelog

## [0.6.4](https://github.com/middag-io/middag-php-ui/compare/v0.6.3...v0.6.4) (2026-06-02)


### Bug Fixes

* **form_panel:** emit required errors + meta in the block payload ([122faa0](https://github.com/middag-io/middag-php-ui/commit/122faa0dc0ed15dacd0636653af1ee404ec62adc))


### Miscellaneous

* strip stale ADR refs + tag schema-emitter internals + .specify export-ignore ([7478d1f](https://github.com/middag-io/middag-php-ui/commit/7478d1ff1b1ffa894fb2cbeef87847cc4108c1d8))

## [0.6.3](https://github.com/middag-io/middag-php-ui/compare/v0.6.2...v0.6.3) (2026-06-01)


### Features

* **field-type:** add slider, otp, native_select field types ([d7ef372](https://github.com/middag-io/middag-php-ui/commit/d7ef37224d4f73a89124b98f3d107f8f98cbacc2))

## [0.6.2](https://github.com/middag-io/middag-php-ui/compare/v0.6.1...v0.6.2) (2026-06-01)


### Features

* **block:** remember flag on BlockDescriptor for Inertia v3 useRemember ([943f630](https://github.com/middag-io/middag-php-ui/commit/943f63096cc8cccabeee6780bbb78e8677cb795b))

## [0.6.1](https://github.com/middag-io/middag-php-ui/compare/v0.6.0...v0.6.1) (2026-06-01)


### Features

* **block:** deferred flag on BlockDescriptor for Inertia v3 deferred props ([2c0ed72](https://github.com/middag-io/middag-php-ui/commit/2c0ed72f7a7d3c49ef91a67b3d8af703e5c262bf))


### Documentation

* refresh README for v0.6.0 + OSS framing ([274a01b](https://github.com/middag-io/middag-php-ui/commit/274a01bbd5f370037c840c1be5d313415376620a))

## [0.6.0](https://github.com/middag-io/middag-php-ui/compare/v0.5.0...v0.6.0) (2026-05-29)


### ⚠ BREAKING CHANGES

* every Middag\Ui\* FQN changes (stereotype-first layout replaced by concern-first). Pre-1.0, no compatibility shims. Consumers must update imports to the new namespaces.

### Features

* emit JSON Schema from value objects ([4948765](https://github.com/middag-io/middag-php-ui/commit/4948765b24f05a3c6f6e3f03ffdf1b48acbf86a4))


### Refactoring

* reorganize ui to concern-first ([51d35dd](https://github.com/middag-io/middag-php-ui/commit/51d35dd6e98481d8251ec27f5be8816769e2c9a8))
* reorganize ui to concern-first (BREAKING → 0.6.0) ([87b01cc](https://github.com/middag-io/middag-php-ui/commit/87b01cc549900225c8eda8830b76b2943050a77d))

## [0.5.0](https://github.com/middag-io/middag-php-ui/compare/v0.4.0...v0.5.0) (2026-05-29)


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


### Refactoring

* **form:** move layout VOs out of Infrastructure into Data/Form/Layout ([4af118e](https://github.com/middag-io/middag-php-ui/commit/4af118eaf3e43a235cd32f7d228c8b0c7811e306))
* **page:** make AbstractPage::build() a default-throw instead of abstract ([9640a47](https://github.com/middag-io/middag-php-ui/commit/9640a47da56842613eb7c4cf6e776f2df9adda13))
* **ui:** apply omit-empty to NavigationNode and TableConfig ([c38ce6e](https://github.com/middag-io/middag-php-ui/commit/c38ce6e55cd3301734047c88a36173133b1800e0))
* **ui:** camelCase internal props and named-args ([409a4b4](https://github.com/middag-io/middag-php-ui/commit/409a4b4ffd657764523287d509a16bd3c9801ab7))
* **ui:** camelCase the last snake_case wire keys and params ([3df91af](https://github.com/middag-io/middag-php-ui/commit/3df91af568e2c699c9bb709fb5eb21f9f88d3629))
* **ui:** consolidate layer structure and decouple host resolver ([92df7b8](https://github.com/middag-io/middag-php-ui/commit/92df7b80fd81fe1d12bacda7718550fc0b8c5219))
* **ui:** flatten Contract and Data into single per-role dirs ([4f68705](https://github.com/middag-io/middag-php-ui/commit/4f68705c3dc16df16de49764e55ebe8cf635d545))
* **ui:** make Group, Section, FormState immutable readonly VOs ([d18c6c9](https://github.com/middag-io/middag-php-ui/commit/d18c6c99c88e5ef8e06069f61acd29401f7dba21))
* **ui:** RegionBuilder delegates to BlockBuilder factories ([ca88ed2](https://github.com/middag-io/middag-php-ui/commit/ca88ed23b4ae6f14643adc1e857d9e11aace8800))
* **ui:** remove host-specific leaks and add CI ([c91f39d](https://github.com/middag-io/middag-php-ui/commit/c91f39de281e27c103a3ded70c1c0f007e85ca65))
* **ui:** remove redundant CrudControllerSupport and dissolve Support/ layer ([83f3582](https://github.com/middag-io/middag-php-ui/commit/83f3582896bc142ab485847ba664abda6362e883))
* **ui:** Section.label aligns with the canonical label shape ([bbc32af](https://github.com/middag-io/middag-php-ui/commit/bbc32af19596b6b191cf43e01344a9f6307dee58))
* **ui:** tighten public surface and enforce conventions ([d349304](https://github.com/middag-io/middag-php-ui/commit/d349304bdec3d5381f9db5ff5dda0aa2be7364bb))
* **ui:** unify actions into one canonical Action VO with discriminated target ([384e1e3](https://github.com/middag-io/middag-php-ui/commit/384e1e36061ae092f48fc05e2ddd02a923f7a684))


### Documentation

* **api:** reclassify public surface [@internal](https://github.com/internal) -&gt; [@api](https://github.com/api) ([d3e6c14](https://github.com/middag-io/middag-php-ui/commit/d3e6c140f8c357b3ab01df4803f8e548e6c55bd1))
* **ui:** add Features overview table; fix Section.label example ([1669eee](https://github.com/middag-io/middag-php-ui/commit/1669eeef60fe41b3c40229bae8294e07afc90837))
* **ui:** fix stale README examples, strip host/framework names from docblocks, pin boundary invariant ([198f85a](https://github.com/middag-io/middag-php-ui/commit/198f85a6896956f622473b20c186b681ccf110bc))

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
