# Motive ELD Laravel SDK - Implementation TODO

This document tracks all implementation tasks organized by phase. Mark items with `[x]` when complete.

---

## Phase 1: Foundation (Core Infrastructure) ✅

### 1.1 Package Configuration

- [x] **1.1.1** Update `composer.json` with package metadata, dependencies, and autoloading
- [x] **1.1.2** Create `config/motive.php` configuration file
- [x] **1.1.3** Set up PHPStan configuration (`phpstan.neon`)
- [x] **1.1.4** Set up Laravel Pint configuration (`pint.json`)

### 1.2 Service Provider & Facade

- [x] **1.2.1** Create `src/MotiveServiceProvider.php`
  - [x] Register config file
  - [x] Register singleton bindings
  - [x] Register middleware alias
  - [x] Boot method for publishing config
- [x] **1.2.2** Create `src/MotiveManager.php`
  - [x] Connection management
  - [x] Context modifiers (withTimezone, withMetricUnits, withUserId)
  - [x] Dynamic auth (withApiKey, withOAuth, withTokenStore)
  - [x] Resource accessors (vehicles(), users(), etc.)
  - [x] Raw HTTP methods (get, post, put, patch, delete)
- [x] **1.2.3** Create `src/Facades/Motive.php`

### 1.3 HTTP Client

- [x] **1.3.1** Create `src/Client/MotiveClient.php`
  - [x] Configure Laravel HTTP client
  - [x] Base URL handling
  - [x] Timeout and retry configuration
  - [x] Request/response interceptors
  - [x] Error handling and exception mapping
- [x] **1.3.2** Create `src/Client/PendingRequest.php`
  - [x] Fluent request builder
  - [x] Query parameter handling
  - [x] Request body formatting
  - [x] Header management
- [x] **1.3.3** Create `src/Client/Response.php`
  - [x] Response wrapper with helper methods
  - [x] JSON parsing
  - [x] Status code accessors
  - [x] Header accessors

### 1.4 Contracts

- [x] **1.4.1** Create `src/Contracts/Authenticator.php`
  - [x] `authenticate(PendingRequest $request): PendingRequest`
  - [x] `isExpired(): bool`
  - [x] `refresh(): void`
- [x] **1.4.2** Create `src/Contracts/TokenStore.php`
  - [x] `getAccessToken(): ?string`
  - [x] `getRefreshToken(): ?string`
  - [x] `getExpiresAt(): ?CarbonInterface`
  - [x] `store(string $accessToken, string $refreshToken, CarbonInterface $expiresAt): void`

### 1.5 Authentication

- [x] **1.5.1** Create `src/Auth/ApiKeyAuthenticator.php`
  - [x] Add X-Api-Key header to requests
  - [x] Implement Authenticator contract

### 1.6 Exceptions

- [x] **1.6.1** Create `src/Exceptions/MotiveException.php` (base exception)
  - [x] Store response object
  - [x] `getResponse(): ?Response`
  - [x] `getResponseBody(): ?array`
- [x] **1.6.2** Create `src/Exceptions/AuthenticationException.php` (401)
- [x] **1.6.3** Create `src/Exceptions/AuthorizationException.php` (403)
- [x] **1.6.4** Create `src/Exceptions/NotFoundException.php` (404)
- [x] **1.6.5** Create `src/Exceptions/ValidationException.php` (422)
  - [x] `errors(): array` method for field errors
- [x] **1.6.6** Create `src/Exceptions/RateLimitException.php` (429)
  - [x] `retryAfter(): ?int` method
- [x] **1.6.7** Create `src/Exceptions/ServerException.php` (5xx)

### 1.7 Base Resource

- [x] **1.7.1** Create `src/Resources/Resource.php`
  - [x] Abstract base class
  - [x] Client injection
  - [x] API version handling
  - [x] Abstract methods: `basePath()`, `resourceKey()`, `dtoClass()`
- [x] **1.7.2** Create `src/Resources/Concerns/HasCrudOperations.php`
  - [x] `list(array $params = []): LazyCollection`
  - [x] `paginate(int $page = 1, int $perPage = 25, array $params = []): PaginatedResponse`
  - [x] `find(int|string $id): DataTransferObject`
  - [x] `create(array $data): DataTransferObject`
  - [x] `update(int|string $id, array $data): DataTransferObject`
  - [x] `delete(int|string $id): bool`
- [x] **1.7.3** Create `src/Resources/Concerns/HasPagination.php`
  - [x] Cursor-based pagination support
  - [x] Page-based pagination support
  - [x] Per-page configuration
- [x] **1.7.4** Create `src/Resources/Concerns/HasExternalIdLookup.php`
  - [x] `findByExternalId(string $externalId): DataTransferObject`

### 1.8 Data Transfer Objects

- [x] **1.8.1** Create `src/Data/DataTransferObject.php`
  - [x] Abstract base class
  - [x] `static from(array $data): static` factory method
  - [x] Array/JSON conversion methods
  - [x] Property mapping utilities
- [x] **1.8.2** Create `src/Data/Concerns/HasFactory.php`
  - [x] `static factory(): Factory` method

### 1.9 Pagination

- [x] **1.9.1** Create `src/Pagination/Paginator.php`
  - [x] Core pagination logic
  - [x] Page tracking
  - [x] API request handling
- [x] **1.9.2** Create `src/Pagination/PaginatedResponse.php`
  - [x] `items(): Collection`
  - [x] `total(): int`
  - [x] `perPage(): int`
  - [x] `currentPage(): int`
  - [x] `lastPage(): int`
  - [x] `hasMorePages(): bool`
  - [x] `count(): int`
- [x] **1.9.3** Create `src/Pagination/LazyPaginator.php`
  - [x] Generator-based iteration
  - [x] Automatic page fetching
  - [x] Memory-efficient processing

---

## Phase 2: Essential Resources ✅

### 2.1 Vehicle Resource

- [x] **2.1.1** Create `src/Resources/Vehicles/VehiclesResource.php`
  - [x] Standard CRUD operations
  - [x] `findByNumber(string $number): Vehicle`
  - [x] `currentLocation(int|string $id): VehicleLocation`
  - [x] `locations(int|string $id, array $params = []): LazyCollection`
- [x] **2.1.2** Create `src/Data/Vehicle.php`
  - [x] All vehicle properties (id, company_id, number, make, model, year, vin, etc.)
  - [x] Status enum property
  - [x] Current driver relationship
  - [x] Timestamps as Carbon
- [x] **2.1.3** Create `src/Data/VehicleLocation.php`
  - [x] Latitude, longitude, speed, bearing
  - [x] Located at timestamp
  - [x] Address information
- [x] **2.1.4** Create `src/Enums/VehicleStatus.php`
  - [x] Active, Inactive, Decommissioned cases

### 2.2 User Resource

- [x] **2.2.1** Create `src/Resources/Users/UsersResource.php`
  - [x] Standard CRUD operations
  - [x] `deactivate(int|string $id): bool`
  - [x] `reactivate(int|string $id): bool`
- [x] **2.2.2** Create `src/Data/User.php`
  - [x] All user properties
  - [x] Driver relationship (nullable)
  - [x] Role and status enums
- [x] **2.2.3** Create `src/Data/Driver.php`
  - [x] License information
  - [x] ELD-specific fields
  - [x] Carrier information
- [x] **2.2.4** Create `src/Enums/UserRole.php`
- [x] **2.2.5** Create `src/Enums/UserStatus.php`

### 2.3 Asset Resource

- [x] **2.3.1** Create `src/Resources/Assets/AssetsResource.php`
  - [x] Standard CRUD operations
  - [x] `assignToVehicle(int|string $assetId, int|string $vehicleId): bool`
  - [x] `unassignFromVehicle(int|string $assetId): bool`
- [x] **2.3.2** Create `src/Data/Asset.php`
- [x] **2.3.3** Create `src/Enums/AssetStatus.php`
- [x] **2.3.4** Create `src/Enums/AssetType.php`

### 2.4 Company Resource

- [x] **2.4.1** Create `src/Resources/Companies/CompaniesResource.php`
  - [x] `current(): Company` (get authenticated company)
- [x] **2.4.2** Create `src/Data/Company.php`

---

## Phase 3: HOS & Compliance ✅

### 3.1 HOS Logs Resource

- [x] **3.1.1** Create `src/Resources/HoursOfService/HosLogsResource.php`
  - [x] List with date range and driver filters
  - [x] Create log entry
  - [x] Update log entry (with annotation)
  - [x] Delete log entry
  - [x] `certify(int|string $driverId, string $date): bool`
- [x] **3.1.2** Create `src/Data/HosLog.php`
  - [x] Duty status enum
  - [x] Start time, duration
  - [x] Location information
  - [x] Driver relationship
  - [x] Annotations
- [x] **3.1.3** Create `src/Enums/DutyStatus.php`
  - [x] OffDuty, SleeperBerth, Driving, OnDuty, PersonalConveyance, YardMove

### 3.2 HOS Availability Resource

- [x] **3.2.1** Create `src/Resources/HoursOfService/HosAvailabilityResource.php`
  - [x] `list(array $params = []): LazyCollection`
  - [x] `forDriver(int|string $driverId): HosAvailability`
- [x] **3.2.2** Create `src/Data/HosAvailability.php`
  - [x] Drive time remaining
  - [x] Shift time remaining
  - [x] Cycle time remaining
  - [x] Break time required
  - [x] Driver relationship

### 3.3 HOS Violations Resource

- [x] **3.3.1** Create `src/Resources/HoursOfService/HosViolationsResource.php`
  - [x] List with date range and driver filters
- [x] **3.3.2** Create `src/Data/HosViolation.php`
  - [x] Violation type enum
  - [x] Start time, duration
  - [x] Driver relationship
- [x] **3.3.3** Create `src/Enums/HosViolationType.php`

### 3.4 Inspection Reports Resource

- [x] **3.4.1** Create `src/Resources/Inspections/InspectionReportsResource.php`
  - [x] List with filters
  - [x] Find by ID
  - [x] forDriver, forVehicle methods
- [x] **3.4.2** Create `src/Data/InspectionReport.php`
  - [x] Type (pre_trip, post_trip)
  - [x] Status
  - [x] Vehicle and driver relationships
  - [x] Defects collection
- [x] **3.4.3** Create `src/Data/InspectionDefect.php`
- [x] **3.4.4** Create `src/Enums/InspectionType.php`
- [x] **3.4.5** Create `src/Enums/InspectionStatus.php`

### 3.5 Fault Codes Resource

- [x] **3.5.1** Create `src/Resources/Vehicles/FaultCodesResource.php`
- [x] **3.5.2** Create `src/Data/FaultCode.php`

---

## Phase 4: Dispatch & Location ✅

### 4.1 Dispatches Resource

- [x] **4.1.1** Create `src/Resources/Dispatches/DispatchesResource.php`
  - [x] Standard CRUD operations
  - [x] Status filters
- [x] **4.1.2** Create `src/Data/Dispatch.php`
  - [x] External ID
  - [x] Status enum
  - [x] Driver and vehicle relationships
  - [x] Stops collection
- [x] **4.1.3** Create `src/Data/DispatchStop.php`
- [x] **4.1.4** Create `src/Enums/DispatchStatus.php`
  - [x] Pending, InProgress, Completed, Cancelled
- [x] **4.1.5** Create `src/Enums/StopType.php`
  - [x] Pickup, Delivery, Waypoint

### 4.2 Locations Resource

- [x] **4.2.1** Create `src/Resources/Locations/LocationsResource.php`
  - [x] Standard CRUD operations
  - [x] `findNearest(float $lat, float $lng, int $radius = 1000): Collection`
- [x] **4.2.2** Create `src/Data/Location.php`

### 4.3 Geofences Resource

- [x] **4.3.1** Create `src/Resources/Geofences/GeofencesResource.php`
  - [x] Standard CRUD operations
  - [x] Support for circle and polygon types
- [x] **4.3.2** Create `src/Data/Geofence.php`
- [x] **4.3.3** Create `src/Data/GeofenceCoordinate.php`
- [x] **4.3.4** Create `src/Enums/GeofenceType.php`

### 4.4 Groups Resource

- [x] **4.4.1** Create `src/Resources/Groups/GroupsResource.php`
  - [x] Standard CRUD operations
  - [x] `addMember(int|string $groupId, int|string $memberId, string $memberType): bool`
  - [x] `removeMember(int|string $groupId, int|string $memberId): bool`
  - [x] `members(int|string $groupId): Collection`
- [x] **4.4.2** Create `src/Data/Group.php`
- [x] **4.4.3** Create `src/Data/GroupMember.php`

---

## Phase 5: OAuth & Webhooks ✅

### 5.1 OAuth Authentication

- [x] **5.1.1** Create `src/Auth/OAuthAuthenticator.php`
  - [x] Bearer token authentication
  - [x] Automatic token refresh when expired
  - [x] Token store integration
- [x] **5.1.2** Create `src/Auth/AccessToken.php`
  - [x] Access token, refresh token, expires at
  - [x] `isExpired(): bool`
- [x] **5.1.3** Create `src/Auth/OAuthFlow.php`
  - [x] `authorizationUrl(array $scopes, ?string $state = null): string`
  - [x] `exchangeCode(string $code): AccessToken`
  - [x] `refreshToken(string $refreshToken): AccessToken`
- [x] **5.1.4** Create `src/Enums/Scope.php`
  - [x] All OAuth scopes as enum cases

### 5.2 Webhooks

- [x] **5.2.1** Create `src/Webhooks/WebhookSignature.php`
  - [x] `verify(string $payload, string $signature, string $secret): bool`
  - [x] `generate(string $payload, string $secret): string`
  - [x] Timestamp tolerance validation
- [x] **5.2.2** Create `src/Webhooks/WebhookPayload.php`
  - [x] `static fromRequest(Request $request): static`
  - [x] Event type enum property
  - [x] Data array accessor
  - [x] Timestamp
- [x] **5.2.3** Create `src/Http/Middleware/VerifyWebhookSignature.php`
  - [x] Verify signature header
  - [x] Throw WebhookVerificationException on failure
- [x] **5.2.4** Create `src/Exceptions/WebhookVerificationException.php`

### 5.3 Webhooks Resource

- [x] **5.3.1** Create `src/Resources/Webhooks/WebhooksResource.php`
  - [x] Standard CRUD operations
  - [x] `test(int|string $id): bool`
  - [x] `logs(int|string $id): Collection`
- [x] **5.3.2** Create `src/Data/Webhook.php`
- [x] **5.3.3** Create `src/Data/WebhookLog.php`
- [x] **5.3.4** Create `src/Enums/WebhookEvent.php`
  - [x] All webhook event types
- [x] **5.3.5** Create `src/Enums/WebhookStatus.php`

---

## Phase 6: Communication & Documents ✅

### 6.1 Messages Resource

- [x] **6.1.1** Create `src/Resources/Messages/MessagesResource.php`
  - [x] List with driver filter
  - [x] Find by ID
  - [x] `send(array $data): Message`
  - [x] `broadcast(array $data): Collection`
- [x] **6.1.2** Create `src/Data/Message.php`
- [x] **6.1.3** Create `src/Enums/MessageDirection.php`

### 6.2 Documents Resource

- [x] **6.2.1** Create `src/Resources/Documents/DocumentsResource.php`
  - [x] List with filters
  - [x] Find by ID
  - [x] `upload(array $data): Document`
  - [x] `download(int|string $id): string`
  - [x] `updateStatus(int|string $id, DocumentStatus $status): Document`
  - [x] Delete
- [x] **6.2.2** Create `src/Data/Document.php`
- [x] **6.2.3** Create `src/Data/DocumentImage.php`
- [x] **6.2.4** Create `src/Enums/DocumentType.php`
- [x] **6.2.5** Create `src/Enums/DocumentStatus.php`

---

## Phase 7: Fuel & Reporting ✅

### 7.1 Fuel Purchases Resource

- [x] **7.1.1** Create `src/Resources/FuelPurchases/FuelPurchasesResource.php`
  - [x] Standard CRUD operations
  - [x] Date range and vehicle filters
- [x] **7.1.2** Create `src/Data/FuelPurchase.php`

### 7.2 IFTA Reports Resource

- [x] **7.2.1** Create `src/Resources/IftaReports/IftaReportsResource.php`
  - [x] `generate(array $params): IftaReport`
  - [x] `list(array $params = []): LazyCollection`
- [x] **7.2.2** Create `src/Data/IftaReport.php`
- [x] **7.2.3** Create `src/Data/IftaJurisdiction.php`

### 7.3 Driver Performance Resource

- [x] **7.3.1** Create `src/Resources/DriverPerformance/DriverPerformanceEventsResource.php`
  - [x] List with filters
  - [x] Find by ID
- [x] **7.3.2** Create `src/Data/DriverPerformanceEvent.php`
- [x] **7.3.3** Create `src/Enums/PerformanceEventType.php`
- [x] **7.3.4** Create `src/Enums/EventSeverity.php`

### 7.4 Scorecard Resource

- [x] **7.4.1** Create `src/Resources/Scorecard/ScorecardResource.php`
  - [x] `forDriver(int|string $driverId, array $params = []): Scorecard`
  - [x] `forFleet(array $params = []): Scorecard`
- [x] **7.4.2** Create `src/Data/Scorecard.php`

### 7.5 Utilization Resource

- [x] **7.5.1** Create `src/Resources/Utilization/UtilizationResource.php`
  - [x] `forVehicle(int|string $vehicleId, array $params = []): UtilizationReport`
  - [x] `forFleet(array $params = []): UtilizationReport`
  - [x] `daily(array $params = []): Collection`
  - [x] `summary(array $params = []): UtilizationReport`
- [x] **7.5.2** Create `src/Data/UtilizationReport.php`
- [x] **7.5.3** Create `src/Data/UtilizationDay.php`

---

## Phase 8: Time & Forms ✅

### 8.1 Timecards Resource

- [x] **8.1.1** Create `src/Resources/Timecards/TimecardsResource.php`
  - [x] List with filters
  - [x] Find by ID
  - [x] Update
  - [x] forDriver filter
- [x] **8.1.2** Create `src/Data/Timecard.php`
- [x] **8.1.3** Create `src/Data/TimecardEntry.php`
- [x] **8.1.4** Create `src/Enums/TimecardStatus.php`

### 8.2 Forms Resource

- [x] **8.2.1** Create `src/Resources/Forms/FormsResource.php`
  - [x] List available forms
  - [x] Find by ID
  - [x] Create
  - [x] Update
  - [x] Delete
  - [x] Active filter
- [x] **8.2.2** Create `src/Data/Form.php`
- [x] **8.2.3** Create `src/Data/FormField.php`
- [x] **8.2.4** Create `src/Enums/FormFieldType.php`

### 8.3 Form Entries Resource

- [x] **8.3.1** Create `src/Resources/FormEntries/FormEntriesResource.php`
  - [x] List with filters
  - [x] Find by ID
  - [x] Create
  - [x] forForm filter
  - [x] forDriver filter
- [x] **8.3.2** Create `src/Data/FormEntry.php`

### 8.4 Driving Periods Resource

- [x] **8.4.1** Create `src/Resources/DrivingPeriods/DrivingPeriodsResource.php`
  - [x] `list(array $params = []): LazyCollection`
  - [x] `find(int|string $id): DrivingPeriod`
  - [x] `forDriver(int|string $driverId): LazyCollection`
  - [x] `forVehicle(int|string $vehicleId): LazyCollection`
  - [x] `forDateRange(string $startDate, string $endDate): LazyCollection`
- [x] **8.4.2** Create `src/Data/DrivingPeriod.php`

---

## Phase 9: Advanced Resources ✅

### 9.1 Motive Card Resource

- [x] **9.1.1** Create `src/Resources/MotiveCard/MotiveCardResource.php`
  - [x] List cards
  - [x] `transactions(int|string $cardId, array $params = []): Collection`
  - [x] `limits(int|string $cardId): CardLimit`
- [x] **9.1.2** Create `src/Data/MotiveCard.php`
- [x] **9.1.3** Create `src/Data/CardTransaction.php`
- [x] **9.1.4** Create `src/Data/CardLimit.php`
- [x] **9.1.5** Create `src/Enums/CardTransactionType.php`

### 9.2 Freight Visibility Resource

- [x] **9.2.1** Create `src/Resources/FreightVisibility/FreightVisibilityResource.php`
  - [x] `shipments(array $params = []): LazyCollection`
  - [x] `tracking(string $shipmentId): ShipmentTracking`
  - [x] `eta(string $shipmentId): ShipmentEta`
- [x] **9.2.2** Create `src/Data/Shipment.php`
- [x] **9.2.3** Create `src/Data/ShipmentTracking.php`
- [x] **9.2.4** Create `src/Data/ShipmentEta.php`
- [x] **9.2.5** Create `src/Enums/ShipmentStatus.php`

### 9.3 Camera Resources

- [x] **9.3.1** Create `src/Resources/Camera/CameraConnectionsResource.php`
  - [x] List camera connections
- [x] **9.3.2** Create `src/Resources/Camera/CameraControlResource.php`
  - [x] `requestVideo(array $params): VideoRequest`
  - [x] `getVideo(string $requestId): Video`
- [x] **9.3.3** Create `src/Data/CameraConnection.php`
- [x] **9.3.4** Create `src/Data/VideoRequest.php`
- [x] **9.3.5** Create `src/Data/Video.php`
- [x] **9.3.6** Create `src/Enums/CameraType.php`
- [x] **9.3.7** Create `src/Enums/VideoStatus.php`

### 9.4 External IDs Resource

- [x] **9.4.1** Create `src/Resources/ExternalIds/ExternalIdsResource.php`
  - [x] `set(string $resourceType, int|string $resourceId, string $externalId): bool`
  - [x] `get(string $resourceType, int|string $resourceId): ?string`
  - [x] `delete(string $resourceType, int|string $resourceId): bool`
- [x] **9.4.2** Create `src/Data/ExternalId.php`

### 9.5 Vehicle Gateways Resource

- [x] **9.5.1** Create `src/Resources/VehicleGateways/VehicleGatewaysResource.php`
  - [x] List gateways
- [x] **9.5.2** Create `src/Data/VehicleGateway.php`

### 9.6 Reefer Activity Resource

- [x] **9.6.1** Create `src/Resources/ReeferActivity/ReeferActivityResource.php`
  - [x] `list(array $params = []): LazyCollection`
  - [x] `forVehicle(int|string $vehicleId, array $params = []): Collection`
- [x] **9.6.2** Create `src/Data/ReeferActivity.php`

---

## Phase 10: Testing Infrastructure ✅

### 10.1 Testing Core

- [x] **10.1.1** Create `src/Testing/MotiveFake.php`
  - [x] Replace real client with fake
  - [x] Configure fake responses
  - [x] Track request history
  - [x] Assertion methods
- [x] **10.1.2** Create `src/Testing/FakeResponse.php`
  - [x] `static json(array $data): static`
  - [x] `static paginated(array $items, int $total, int $perPage): static`
  - [x] `static error(int $status, array $body): static`
  - [x] `static empty(): static`
- [x] **10.1.3** Create `src/Testing/RequestHistory.php`
  - [x] Store and query recorded requests
  - [x] Assertion helpers

### 10.2 Factories

- [x] **10.2.1** Create `src/Testing/Factories/Factory.php` (base class)
  - [x] `make(array $attributes = []): DataTransferObject`
  - [x] `count(int $count): static`
  - [x] State methods
- [x] **10.2.2** Create `src/Testing/Factories/VehicleFactory.php`
- [x] **10.2.3** Create `src/Testing/Factories/UserFactory.php`
- [x] **10.2.4** Create `src/Testing/Factories/DriverFactory.php`
- [x] **10.2.5** Create `src/Testing/Factories/AssetFactory.php`
- [x] **10.2.6** Create `src/Testing/Factories/HosLogFactory.php`
- [x] **10.2.7** Create `src/Testing/Factories/HosAvailabilityFactory.php`
- [x] **10.2.8** Create `src/Testing/Factories/DispatchFactory.php`
- [x] **10.2.9** Create `src/Testing/Factories/LocationFactory.php`
- [x] **10.2.10** Create `src/Testing/Factories/GeofenceFactory.php`
- [x] **10.2.11** Create `src/Testing/Factories/DocumentFactory.php`
- [x] **10.2.12** Create `src/Testing/Factories/MessageFactory.php`
- [x] **10.2.13** Create `src/Testing/Factories/WebhookFactory.php`
- [x] **10.2.14** Create `src/Testing/Factories/InspectionReportFactory.php`
- [x] **10.2.15** Create `src/Testing/Factories/FuelPurchaseFactory.php`

### 10.3 Test Suite Setup

- [x] **10.3.1** Create `tests/TestCase.php`
  - [x] PHPUnit configuration
  - [x] Helper methods
  - [x] Test traits
- [x] **10.3.2** Create `phpunit.xml` configuration

### 10.4 Unit Tests

- [x] **10.4.1** Create `tests/Unit/ClientTest.php`
  - [x] Test HTTP client configuration
  - [x] Test request building
  - [x] Test response parsing
- [x] **10.4.2** Create `tests/Unit/PaginationTest.php`
  - [x] Test paginated response
  - [x] Test lazy pagination
  - [x] Test cursor pagination
- [x] **10.4.3** Create `tests/Unit/AuthenticationTest.php`
  - [x] Test API key auth
  - [x] Test OAuth auth
  - [x] Test token refresh
- [x] **10.4.4** Create `tests/Unit/WebhookSignatureTest.php`
  - [x] Test signature verification
  - [x] Test signature generation
  - [x] Test timestamp tolerance
- [x] **10.4.5** Create `tests/Unit/DataTransferObjectTest.php`
  - [x] Test DTO hydration
  - [x] Test nested relationships
  - [x] Test enum casting
  - [x] Test date casting

### 10.5 Feature Tests

- [x] **10.5.1** Create `tests/Feature/VehiclesResourceTest.php`
- [x] **10.5.2** Create `tests/Feature/UsersResourceTest.php`
- [x] **10.5.3** Create `tests/Feature/HosResourceTest.php`
- [x] **10.5.4** Create `tests/Feature/DispatchesResourceTest.php`
- [x] **10.5.5** Create `tests/Feature/WebhooksResourceTest.php`
- [x] **10.5.6** Create `tests/Feature/OAuthFlowTest.php`
- [x] **10.5.7** Create `tests/Feature/MultiTenancyTest.php`

---

## Phase 11: Documentation & Polish ✅

### 11.1 Laravel Fluent DTO Upgrade

- [x] **11.1.1** Use Laravel Fluent from illuminate/support (built-in)
- [x] **11.1.2** Upgrade all 50+ DTOs to use Laravel Fluent with $casts array
- [x] **11.1.3** Update DataTransferObject base class to extend Fluent
- [x] **11.1.4** Add $defaults property support for default values
- [x] **11.1.5** Run and fix all 762 tests passing

### 11.2 Documentation

- [x] **11.2.1** README.md already has complete API examples
- [x] **11.2.2** Create `CHANGELOG.md`
- [x] **11.2.3** Create `CONTRIBUTING.md`
- [x] **11.2.4** Create `LICENSE.md` (MIT)
- [ ] **11.2.5** Laravel Boost supported package documentation (optional)

### 11.3 PHPDoc

- [x] **11.3.1** PHPDoc on MotiveManager methods
- [x] **11.3.2** PHPDoc on Resource classes
- [x] **11.3.3** @property PHPDoc on all DTOs for IDE support
- [x] **11.3.4** PHPDoc on Enums
- [x] **11.3.5** PHPDoc on Testing classes

### 11.4 Code Quality

- [x] **11.4.1** PHPStan at level 6 passing (with appropriate ignores for generic types)
- [x] **11.4.2** Laravel Pint passing
- [x] **11.4.3** 762 tests, 2422 assertions
- [x] **11.4.4** Exception classes have clear messages

### 11.5 Final Review

- [x] **11.5.1** Resource method signatures are consistent
- [x] **11.5.2** DTO properties are complete with type casting
- [x] **11.5.3** Enum cases follow API documentation patterns
- [ ] **11.5.4** Test full integration with real Motive API (requires credentials)
- [ ] **11.5.5** Performance testing with large datasets (optional)
- [x] **11.5.6** v1.0.0 release prepared

---

## Progress Summary

| Phase | Total Items | Completed | Progress |
|-------|-------------|-----------|----------|
| Phase 1: Foundation | 28 | 28 | 100% |
| Phase 2: Essential Resources | 14 | 14 | 100% |
| Phase 3: HOS & Compliance | 15 | 15 | 100% |
| Phase 4: Dispatch & Location | 13 | 13 | 100% |
| Phase 5: OAuth & Webhooks | 14 | 14 | 100% |
| Phase 6: Communication & Documents | 9 | 9 | 100% |
| Phase 7: Fuel & Reporting | 12 | 12 | 100% |
| Phase 8: Time & Forms | 10 | 10 | 100% |
| Phase 9: Advanced Resources | 18 | 18 | 100% |
| Phase 10: Testing Infrastructure | 25 | 25 | 100% |
| Phase 11: Documentation & Polish | 15 | 13 | 87% |
| **Total** | **173** | **171** | **99%** |

---

## Notes

- Each phase builds upon the previous phases
- Phase 1 (Foundation) must be completed before any other phase
- Phases 2-4 can be worked on in parallel after Phase 1
- Phase 5 (OAuth & Webhooks) can be started after Phase 1
- Phases 6-9 can be worked on in parallel after Phase 2
- Phase 10 (Testing) should be incrementally built alongside other phases
- Phase 11 (Documentation) is ongoing throughout development
