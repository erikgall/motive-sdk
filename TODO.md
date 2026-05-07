# TODO

## Laravel 13 Support — `feat/laravel-13-support`

- [x] Widen `composer.json` to `^12.47|^13.0` for `illuminate/*`; widen testbench/phpunit; add larastan; bump pint and phpstan floor.
- [x] Fix `FakeHttpResponse::json()` LSP compatibility with Laravel 13's new `$flags` parameter.
- [x] Clamp `PaginatedResponse::count()` to non-negative.
- [x] Switch `VerifyWebhookSignature` to `$request->headers->get(...)` and add `@phpstan-consistent-constructor`.
- [x] Annotate `DataTransferObject` and `WebhookSignature` with `@phpstan-consistent-constructor`.
- [x] Wire Larastan into `phpstan.neon`; verify level 6 stays clean.
- [x] Create `.github/workflows/tests.yml` matrix covering PHP 8.2/8.3/8.4 × Laravel 12/13.
- [x] Update `CHANGELOG.md` and `README.md`.
- [x] Mirror to `PLAN.md` and `TODO.md`.
- [x] Run prefer-stable + prefer-lowest verification locally on the Laravel 12 path.
- [x] Push branch and open PR (#1).
- [x] Add `"license": "MIT"` after CI flagged the missing field via `composer validate --strict`.
- [x] Bump `laravel/pint` floor to `^1.29.1` after CI prefer-lowest leg surfaced a v1.29.0 fixer regression.
- [x] All six matrix legs green.
- [ ] Merge PR #1.

## Deferred (not blocking the L13 release)

- [ ] Resolve 3 PHPUnit deprecations from `with*()` on test stubs (`RateLimitExceptionTest`, `ValidationExceptionTest`) before PHPUnit 13.
