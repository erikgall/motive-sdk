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
- [ ] Run prefer-stable + prefer-lowest verification locally on the Laravel 12 path (current lockfile resolved to L13).
- [ ] Push branch and open PR.
- [ ] Wait for green CI across all six matrix legs before merging.

## Deferred (not blocking the L13 release)

- [ ] Resolve 3 PHPUnit deprecations from `with*()` on test stubs (`RateLimitExceptionTest`, `ValidationExceptionTest`) before PHPUnit 13.
