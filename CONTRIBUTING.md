# Contributing to enlivy-php

Thanks for helping improve the Enlivy PHP SDK. Start with
[AGENTS.md](AGENTS.md) — it is the canonical guide to the architecture,
conventions, and the two non-negotiable principles for this repository.

## Development setup

```bash
git clone https://github.com/enlivy/enlivy-php.git
cd enlivy-php
composer install
```

## Quality gate

Every change must pass before it is submitted:

```bash
composer test   # PHPUnit (unit suite, no credentials needed)
composer stan   # PHPStan level 5 — zero errors
composer qa     # both
```

The integration suite (`tests/Integration/`) runs against a real account and
is optional for contributors — see [TESTING.md](TESTING.md).

## Pull requests

- Keep changes focused; one concern per PR.
- Follow the service/resource templates in [AGENTS.md](AGENTS.md) — includes
  and filters must match the public API contract exactly.
- Comments are minimal: only a non-obvious *why* earns one.
- Add or extend tests for behavior you change.
- Update the matching page in `docs/` when the public surface changes.
- Do not bump the version or edit `CHANGELOG.md` — maintainers handle
  releases.

## Reporting issues

Use [GitHub issues](https://github.com/enlivy/enlivy-php/issues) for bugs and
feature requests. For security vulnerabilities, see [SECURITY.md](SECURITY.md)
— never open a public issue.
