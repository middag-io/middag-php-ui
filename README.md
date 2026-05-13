# middag-io/ui

MIDDAG UI contract builders — transport-agnostic PageContract system for contract-driven rendering.

## Installation

Add the MIDDAG Private Satis repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://privatesatis.middag.com.br"
        }
    ]
}
```

Then install:

```bash
composer require middag-io/ui
```

## Requirements

- PHP ^8.2
- Zero external dependencies (pure PHP contracts)

## Development

```bash
composer install
```

Git hooks are configured automatically via `post-install-cmd`. The `commit-msg` hook enforces [Conventional Commits](https://www.conventionalcommits.org/).

### Commit format

```
type(scope): description

Types: feat, fix, chore, docs, style, refactor, perf, test, build, ci, revert
```

### Releases

Releases are managed by [release-please](https://github.com/googleapis/release-please). Push conventional commits to `main` and a Release PR is created automatically.

## License

Proprietary — MIDDAG (https://www.middag.com.br)
