<!--
Thanks for contributing to middag-io/ui!
Please fill in the sections below. PRs target the `main` branch.
-->

## Summary

<!-- What does this PR change, and why? Keep it focused on a single concern. -->

## Related issues

<!-- Link issues this PR addresses. Use "Closes #123" to auto-close on merge. -->

Closes #

## Type of change

<!-- Check all that apply. These mirror the Conventional Commit type used in your commit messages. -->

- [ ] `feat` — a new feature
- [ ] `fix` — a bug fix
- [ ] `refactor` — a code change that neither fixes a bug nor adds a feature
- [ ] `docs` — documentation only
- [ ] `test` — adding or correcting tests
- [ ] `chore` — tooling, build, or maintenance changes
- [ ] `perf` — a performance improvement
- [ ] `style` — formatting only (no behavior change)

## Breaking change

<!--
Does this PR change public API or the wire contract in a backward-incompatible
way? If so, describe the break below. Rules (ADR-032, see CONTRIBUTING.md
§Versioning):

- On `1.x`: use `feat:` and describe the break in the commit body; never `!`,
  a `BREAKING CHANGE:` footer or `Release-As:`. Allowed only until 2026-11-30;
  from 2026-12-01 no break lands on `1.x`.
- From `2.0.0`: a break needs a family MAJOR window (organization variable
  `PHP_LIBS_OPEN_RELEASE` = `X.0`, set on the order of the product owner) and a deprecation
  in an earlier minor; a `feat` needs a family MINOR window. Until the window
  opens, keep the PR current by merging `main` (never rebase or force-push).
-->

- [ ] This PR introduces a breaking change (described above and in the commit body)
- [ ] The `php-version-family-check / check` job passes

## Checklist

- [ ] Targets the `main` branch
- [ ] `composer check` passes locally
- [ ] `composer test` passes locally
- [ ] Commits follow Conventional Commits
- [ ] No `Co-Authored-By` trailers in any commit
- [ ] Docs / `CHANGELOG.md` updated when relevant
- [ ] New behavior is covered by tests
