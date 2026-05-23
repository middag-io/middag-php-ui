# Contributing

Thanks for your interest in `middag-io/ui`. This package owns the UI vocabulary (contracts, DTOs, enums) shared between `middag-io/framework` and the SPA renderer. Keep contributions narrowly scoped.

## Scope

This library is **contract-first**. It does not own:

- Renderer implementations (Inertia, Mform) — those live in `middag-io/framework` and `middag-io/moodle`.
- Validation engines — `FieldValidator` lives in `middag-io/framework`.
- Persistence — none, by design.

If you want to add behavior, double-check it belongs here. UI contract additions usually start with an ADR.

## Workflow

1. Fork + clone.
2. Create a feature branch off `develop`.
3. `composer check && composer test`.
4. Open PR against `develop`.

## Coding standards

- PHP `^8.2`, `declare(strict_types=1)`.
- PSR-12 + PSR-1 strict camelCase.
- DTOs are `readonly class`.
- Zero external dependencies — UI is a leaf package.

## Tooling

- `composer check` → style + rector + stan dry-runs.
- `composer fix` → apply style + rector.
- `composer fix:all` → style → rector → style.
- `composer test` → PHPUnit.

## Commit messages

Conventional Commits (`feat`, `fix`, `refactor`, `test`, `docs`, `chore`, `style`, `perf`).

## License

Contributions are released under Apache License 2.0.
