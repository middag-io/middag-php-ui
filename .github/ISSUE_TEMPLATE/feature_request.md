---
name: Feature request
about: Suggest an idea or enhancement for middag-io/ui
title: "[Feature] "
labels: enhancement
assignees: ""
---

## Problem / motivation

<!-- What problem does this solve? What are you trying to describe or render that
the contract builders make hard, awkward, or impossible today? Describe the use
case, not just the desired API. -->

## Proposed solution

<!-- Describe the behavior or API you would like. Sketch builders, contracts, or
example usage if it helps. -->

## Alternatives considered

<!-- Other approaches you weighed and why you ruled them out (workarounds,
userland builders, doing nothing). -->

## Scope check

`middag-io/ui` is the transport-agnostic **contract-builder** layer: it produces
the page/form/table/block/navigation/action contracts (the PageContract system)
that hosts and adapters render. It has zero runtime dependencies and knows
nothing about any host. Rendering, host wiring, and HTTP transport live
**downstream** (in `middag-io/framework` and the host adapters), not here.
Please confirm where you believe this feature belongs:

- **Contract surface (this repo)** — a new/extended builder, value object, or
  wire contract that is generic and renderer-agnostic.
- **Renderer / host adapter / your own application** — how a contract is turned
  into HTML/props, or host-specific behaviour built on top of these contracts.

<!-- Tick the one that fits, and add a sentence on why. -->

- [ ] This belongs in the OSS contract layer (generic builders/contracts).
- [ ] This is a renderer or host-specific concern (downstream).
- [ ] Not sure — please help me decide.

## Additional context

<!-- Links, prior art, related issues, the emitted JSON shape, or anything else
that helps us understand the request. -->
