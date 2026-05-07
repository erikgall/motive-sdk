# Active Plans

Per `CLAUDE.md`, this file tracks active and recently completed initiatives. Detailed implementation plans live in `~/.claude/plans/` and are referenced from here.

## Laravel 13 Support — CI Green, Awaiting Merge

**Branch:** `feat/laravel-13-support`
**Plan:** `~/.claude/plans/iterative-wobbling-rainbow.md`
**Reference:** `erikgall/samsara@feat/laravel-13-support` (matched the recipe from the prior upgrade).

**Scope shipped on the branch:**

- Composer constraints widened to `^12.47|^13.0` for `illuminate/*`, `^10.0|^11.0` for `orchestra/testbench`, `^11.0|^12.0` for `phpunit/phpunit`.
- PHPStan floor bumped to `^2.1.44`; `larastan/larastan ^3.9` added; `laravel/pint` bumped to `^1.29`.
- `FakeHttpResponse::json()` accepts the new `$flags` parameter Laravel 13 added to `Illuminate\Http\Client\Response::json()` (LSP fix — caught by full test run after constraint widening).
- `PaginatedResponse::count()` clamped to non-negative.
- `VerifyWebhookSignature` reads request headers via `$request->headers->get(...)`.
- `@phpstan-consistent-constructor` annotation on `DataTransferObject`, `WebhookSignature`, and `VerifyWebhookSignature`.
- Larastan wired into `phpstan.neon`; analysis at level 6 stays clean.
- New GitHub Actions matrix workflow (`.github/workflows/tests.yml`) covering PHP 8.2/8.3/8.4 × L12/L13 plus one prefer-lowest leg.
- `CHANGELOG.md` and `README.md` updated to reflect dual-major support.

**Outstanding:**

- ✅ Local L13 path verified (Laravel 13.8.0 + Testbench 11.1.0 + PHPUnit 12.5.24; 771 tests, PHPStan, Pint all green).
- ✅ Local L12 path verified (Laravel 12.58.0 + Testbench 10.11.0; 771 tests, PHPStan, Pint all green).
- ✅ CI matrix all 6 legs green on PR #1: PHP 8.2/8.3/8.4 × L12 prefer-stable, PHP 8.3/8.4 × L13 prefer-stable, PHP 8.2 × L12 prefer-lowest.
- ⏳ Merge — pending review.

**Hot fixes during execution (added to commit history but not in original plan):**

- `composer.json` was missing a `license` field, causing `composer validate --strict` to fail in CI. Added `"license": "MIT"`.
- `laravel/pint v1.29.0` had a fixer-output regression on `DataTransferObject.php` that `v1.29.1` resolves; bumped floor from `^1.29` to `^1.29.1`.

**Followup deferred from this initiative:**

- 3 PHPUnit deprecations from `with*()` on test stubs in `RateLimitExceptionTest` and `ValidationExceptionTest` (PHPUnit 13 will remove). Unrelated to L13 — track separately when PHPUnit 13 lands.
