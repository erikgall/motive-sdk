# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Support for Laravel 13 alongside Laravel 12 (`illuminate/*: ^12.47|^13.0`).
- GitHub Actions matrix workflow covering PHP 8.2/8.3/8.4 across Laravel 12 and 13, plus one prefer-lowest leg.
- Larastan integration for Laravel-aware static analysis.

### Changed

- `PaginatedResponse::count()` now clamps to a non-negative integer.
- `VerifyWebhookSignature` reads request headers via the Symfony header bag (`$request->headers->get(...)`) for stability across Laravel 12 and 13.
- `FakeHttpResponse::json()` accepts the new `$flags` parameter introduced in Laravel 13's `Illuminate\Http\Client\Response::json()` to remain LSP-compatible across both supported majors.
- `DataTransferObject` and `WebhookSignature` annotated with `@phpstan-consistent-constructor`.
- Bumped `laravel/pint` to `^1.29` (full PHP 8.4 syntax support).
- Raised `phpstan/phpstan` floor to `^2.1.44` (required by Larastan 3.9).

### Compatibility

- Minimum PHP raised explicitly to 8.2 in `composer.json` (was previously implicit). Laravel 13 path requires PHP 8.3+ via the framework's own constraint.
- `orchestra/testbench` widened to `^10.0|^11.0`.
- `phpunit/phpunit` widened to `^11.0|^12.0`.

## [1.0.0] - 2026-01-17

### Added

- Initial release of the Motive ELD Laravel SDK
- Full API coverage with 31 resources supporting 150+ API endpoints
- Fluent, chainable API design following Laravel conventions
- Support for API Key and OAuth 2.0 authentication
- Multi-tenancy support with named connections
- Lazy pagination for memory-efficient data iteration
- Comprehensive type-safe Data Transfer Objects (DTOs) using Laravel Fluent
- Automatic snake_case to camelCase key normalization
- Type casting for enums, Carbon dates, nested DTOs, and primitives
- Default values support for DTO properties
- Webhook handling with signature verification middleware
- Rate limiting with automatic retry support
- Comprehensive exception handling with specific exception types
- Testing support with fakes, factories, and assertions
- PHPStan level 8 static analysis support
- Laravel Pint code style configuration

### Resources

The following API resources are fully implemented:

**Core Resources:**
- Vehicles - List, create, update, delete, location tracking
- Users - User management with driver support
- Drivers - Driver-specific operations
- Assets - Asset management and vehicle assignment
- Companies - Company information

**Compliance:**
- HOS Logs - Hours of Service log management
- HOS Availability - Driver availability tracking
- HOS Violations - Violation monitoring
- Inspection Reports - DVIR management
- Fault Codes - Vehicle fault code tracking

**Dispatch & Location:**
- Dispatches - Dispatch management
- Dispatch Stops - Stop management for dispatches
- Locations - Location management
- Geofences - Geofence creation and management
- Groups - Group and member management

**Communication:**
- Messages - Driver messaging
- Documents - Document upload and management

**Reporting:**
- Fuel Purchases - Fuel tracking
- IFTA Reports - IFTA report generation
- Driver Performance Events - Safety event tracking
- Scorecards - Driver and fleet scoring
- Utilization - Vehicle utilization reports

**Time & Forms:**
- Timecards - Timecard management
- Forms - Form retrieval
- Form Entries - Form submission data
- Driving Periods - Driving period tracking

**Advanced:**
- Motive Card - Fuel card management
- Freight Visibility - Shipment tracking
- Camera Connections - Camera device management
- Camera Control - Video request management
- External IDs - External ID mapping
- Vehicle Gateways - ELD device tracking
- Reefer Activity - Reefer monitoring
- Webhooks - Webhook management with logs

### Data Transfer Objects

50+ type-safe DTOs including:
- Vehicle, VehicleLocation, VehicleGateway
- User, Driver, Company
- Asset
- HosLog, HosAvailability, HosViolation
- Dispatch, DispatchStop, Location, Geofence
- InspectionReport, InspectionDefect, FaultCode
- Document, DocumentImage, Message
- FuelPurchase, IftaReport, IftaJurisdiction
- DriverPerformanceEvent, Scorecard, UtilizationReport
- Timecard, TimecardEntry, Form, FormField, FormEntry
- MotiveCard, CardTransaction, CardLimit
- Shipment, ShipmentTracking, ShipmentEta
- CameraConnection, VideoRequest, Video
- Webhook, WebhookLog
- DrivingPeriod, ReeferActivity, ExternalId, Group, GroupMember

### Enums

Type-safe enums for all status and type fields:
- VehicleStatus, AssetStatus, AssetType
- DutyStatus, HosViolationType
- DispatchStatus, StopType
- DocumentStatus, DocumentType
- MessageDirection
- InspectionType, InspectionStatus
- WebhookEvent, WebhookStatus
- FormFieldType, TimecardStatus
- CardTransactionType, ShipmentStatus
- CameraType, VideoStatus
- PerformanceEventType, EventSeverity
- FuelType, Scope

[Unreleased]: https://github.com/erikgall/motive-sdk/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/erikgall/motive-sdk/releases/tag/v1.0.0
