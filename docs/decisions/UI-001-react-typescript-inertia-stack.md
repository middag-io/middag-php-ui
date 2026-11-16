---
title: 'UI contracts are a separate zero-dependency package at the bottom of the stack'
status: accepted
date: 2026-05-10
last-updated: 2026-11-16
lang: en
domains: [ui, portability]
deciders: [leadership]
decision: 'ui is a separate zero-dependency OSS package at the bottom of the stack; it describes UI and never renders it.'
related: [UI-002, UI-003, middag-php-framework:FW-011, middag-php-framework:FW-004, middag-php-framework:FW-005]
supersedes: []
superseded_by: null
enforced_by:
  mdgstan: []
  tests: [tests/Architecture/RuntimeDependencyTest.php, tests/Architecture/HostVocabularyTest.php]
  docs: [docs/architecture.md]
---

# UI-001: UI contracts are a separate zero-dependency package at the bottom of the stack

## Context

MIDDAG pages are produced on the server by PHP code that runs standalone, inside Moodle and inside WordPress, and are drawn by a client renderer. Something has to define what a page, a form or a table looks like on the wire. If that definition lives next to a renderer, a transport or a host, every consumer inherits that renderer, transport or host, and the client cannot be swapped.

## Considered Options

1. Define the UI contracts inside the framework, next to its Inertia renderer.
2. Define them in each host adapter, in the host's own UI vocabulary.
3. Let the client renderer own the shapes and have the server emit whatever the client expects.
4. A separate OSS package with no dependencies other than PHP, at the bottom of the stack, that emits immutable `JsonSerializable` value objects and never renders, talks to a host or knows the transport; the wire stays renderer-neutral. ← chosen

## Decision

`middag-io/ui` is its own OSS Composer package and requires only PHP. It describes UI as immutable value objects that serialize to JSON and never produces markup, calls a host API or assumes a transport. Every other MIDDAG package may depend on it, and it depends on none of them. Rendering lives downstream: the framework and the host adapters map the contracts to a response, and the first-party client, `@middag-io/react`, is chosen in UI-003. No contract assumes that client, so another renderer can consume the same wire. Authorization and translation travel as opaque data that a downstream layer resolves.

## Consequences

- (+) Consumers inherit no transitive dependency, and the same contract serves every host and every product.
- (+) Server and client evolve separately; a second renderer only has to read the wire.
- (+) Contracts are pure data, so they are testable without a host, a browser or a transport.
- (+) `RendererOutput::html()` and `RenderTarget::Html` carry a markup body that a renderer downstream produced, as opaque data. No ui contract produces markup, so they are not an exception to the decision.
- (+) A file value carries a pending upload as an opaque `uploadToken` that the host adapter maps to its own storage; the wire names no host concept.
- (−) Some docblocks name the first-party client's mechanisms (for example Inertia deferred props on `BlockDescriptor`). The coupling is open in UI-002.
- (−) A change that spans ui, the framework and the client needs coordinated releases across repositories.

## Enforcement

| Decision clause | Verification | State |
|---|---|---|
| Requires only PHP; no runtime dependency | `tests/Architecture/RuntimeDependencyTest.php` | coded |
| No host vocabulary in `src/` and `schema/`, comments included | `tests/Architecture/HostVocabularyTest.php` | coded |
| No transport, no rendering in `src/` | review against the OSS boundary rule in `AGENTS.md` | planned |
| Dependency direction and the OSS boundary | doc `docs/architecture.md` | coded |
