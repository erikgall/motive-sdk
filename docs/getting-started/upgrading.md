# Upgrading

This guide covers upgrading between major versions of the Motive SDK.

## Upgrade Guide

### From 0.x to 1.0

If you're upgrading from a pre-release version, review the following changes:

#### Breaking Changes

1. **PHP Version Requirement**
   - Minimum PHP version is now 8.2

2. **Laravel Version Requirement**
   - Minimum Laravel version is now 11.0

3. **Namespace Changes**
   - The root namespace is `Motive`
   - Facades are at `Motive\Facades\Motive`

4. **Configuration File**
   - Re-publish the configuration file after upgrading:
   ```bash
   php artisan vendor:publish --tag=motive-config --force
   ```

5. **DTO Property Access**
   - All DTOs now use CarbonImmutable for datetime properties
   - Enum properties return actual enum instances instead of strings

#### Migration Steps

1. **Update composer.json**
   ```json
   {
       "require": {
           "erikgall/motive-sdk": "^1.0"
       }
   }
   ```

2. **Run composer update**
   ```bash
   composer update erikgall/motive-sdk
   ```

3. **Update imports**
   ```php
   // Before
   use Motive\Motive;

   // After
   use Motive\Facades\Motive;
   ```

4. **Update enum comparisons**
   ```php
   // Before
   if ($vehicle->status === 'active') {

   // After
   use Motive\Enums\VehicleStatus;
   if ($vehicle->status === VehicleStatus::Active) {
   ```

5. **Update datetime handling**
   ```php
   // DTOs now return CarbonImmutable
   $vehicle->createdAt; // CarbonImmutable instance
   ```

6. **Re-publish configuration**
   ```bash
   php artisan vendor:publish --tag=motive-config --force
   ```

7. **Clear caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Version Compatibility

| SDK Version | PHP Version | Laravel Version |
|-------------|-------------|-----------------|
| 1.x         | 8.2+        | 11.x            |

## Deprecation Notices

The SDK follows semantic versioning. Deprecated features will:

1. Be marked with `@deprecated` PHPDoc tags
2. Trigger deprecation warnings when used
3. Be removed in the next major version

## Getting Help

If you encounter issues during the upgrade:

1. Check the [changelog](changelog.md) for detailed changes
2. Search [existing issues](https://github.com/erikgall/motive-sdk/issues) on GitHub
3. Open a new issue with your upgrade scenario
