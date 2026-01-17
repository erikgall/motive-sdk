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

## Phase 3: HOS & Compliance

### 3.1 HOS Logs Resource

- [ ] **3.1.1** Create `src/Resources/HoursOfService/HosLogsResource.php`
  - [ ] List with date range and driver filters
  - [ ] Create log entry
  - [ ] Update log entry (with annotation)
  - [ ] Delete log entry
  - [ ] `certify(int|string $driverId, string $date): bool`
- [ ] **3.1.2** Create `src/Data/HosLog.php`
  - [ ] Duty status enum
  - [ ] Start time, duration
  - [ ] Location information
  - [ ] Driver relationship
  - [ ] Annotations
- [ ] **3.1.3** Create `src/Enums/DutyStatus.php`
  - [ ] OffDuty, SleeperBerth, Driving, OnDuty, PersonalConveyance, YardMove

### 3.2 HOS Availability Resource

- [ ] **3.2.1** Create `src/Resources/HoursOfService/HosAvailabilityResource.php`
  - [ ] `list(array $params = []): LazyCollection`
  - [ ] `forDriver(int|string $driverId): HosAvailability`
- [ ] **3.2.2** Create `src/Data/HosAvailability.php`
  - [ ] Drive time remaining
  - [ ] Shift time remaining
  - [ ] Cycle time remaining
  - [ ] Break time required
  - [ ] Driver relationship

### 3.3 HOS Violations Resource

- [ ] **3.3.1** Create `src/Resources/HoursOfService/HosViolationsResource.php`
  - [ ] List with date range and driver filters
- [ ] **3.3.2** Create `src/Data/HosViolation.php`
  - [ ] Violation type enum
  - [ ] Start time, duration
  - [ ] Driver relationship
- [ ] **3.3.3** Create `src/Enums/HosViolationType.php`

### 3.4 Inspection Reports Resource

- [ ] **3.4.1** Create `src/Resources/InspectionReports/InspectionReportsResource.php`
  - [ ] List with filters
  - [ ] Find by ID
  - [ ] `downloadPdf(int|string $id): string`
- [ ] **3.4.2** Create `src/Data/InspectionReport.php`
  - [ ] Type (pre_trip, post_trip)
  - [ ] Status
  - [ ] Vehicle and driver relationships
  - [ ] Defects collection
- [ ] **3.4.3** Create `src/Data/InspectionDefect.php`
- [ ] **3.4.4** Create `src/Enums/InspectionType.php`
- [ ] **3.4.5** Create `src/Enums/InspectionStatus.php`

### 3.5 Fault Codes Resource

- [ ] **3.5.1** Create `src/Resources/FaultCodes/FaultCodesResource.php`
- [ ] **3.5.2** Create `src/Data/FaultCode.php`

---

## Phase 4: Dispatch & Location

### 4.1 Dispatches Resource

- [ ] **4.1.1** Create `src/Resources/Dispatches/DispatchesResource.php`
  - [ ] Standard CRUD operations
  - [ ] Status filters
- [ ] **4.1.2** Create `src/Data/Dispatch.php`
  - [ ] External ID
  - [ ] Status enum
  - [ ] Driver and vehicle relationships
  - [ ] Stops collection
- [ ] **4.1.3** Create `src/Data/DispatchStop.php`
- [ ] **4.1.4** Create `src/Enums/DispatchStatus.php`
  - [ ] Pending, InProgress, Completed, Cancelled
- [ ] **4.1.5** Create `src/Enums/StopType.php`
  - [ ] Pickup, Delivery, Waypoint

### 4.2 Dispatch Locations Resource

- [ ] **4.2.1** Create `src/Resources/DispatchLocations/DispatchLocationsResource.php`
  - [ ] CRUD scoped to dispatch
  - [ ] `create(int|string $dispatchId, array $data): DispatchLocation`
- [ ] **4.2.2** Create `src/Data/DispatchLocation.php`

### 4.3 Locations Resource

- [ ] **4.3.1** Create `src/Resources/Locations/LocationsResource.php`
  - [ ] Standard CRUD operations
  - [ ] `findNearest(float $lat, float $lng, int $radius = 1000): Collection`
- [ ] **4.3.2** Create `src/Data/Location.php`

### 4.4 Geofences Resource

- [ ] **4.4.1** Create `src/Resources/Geofences/GeofencesResource.php`
  - [ ] Standard CRUD operations
  - [ ] Support for circle and polygon types
- [ ] **4.4.2** Create `src/Data/Geofence.php`
- [ ] **4.4.3** Create `src/Data/GeofenceCoordinate.php`
- [ ] **4.4.4** Create `src/Enums/GeofenceType.php`

### 4.5 Groups Resource

- [ ] **4.5.1** Create `src/Resources/Groups/GroupsResource.php`
  - [ ] Standard CRUD operations
  - [ ] `addMember(int|string $groupId, int|string $memberId, string $memberType): bool`
  - [ ] `removeMember(int|string $groupId, int|string $memberId): bool`
  - [ ] `members(int|string $groupId): Collection`
- [ ] **4.5.2** Create `src/Data/Group.php`
- [ ] **4.5.3** Create `src/Data/GroupMember.php`

---

## Phase 5: OAuth & Webhooks

### 5.1 OAuth Authentication

- [ ] **5.1.1** Create `src/Auth/OAuthAuthenticator.php`
  - [ ] Bearer token authentication
  - [ ] Automatic token refresh when expired
  - [ ] Token store integration
- [ ] **5.1.2** Create `src/Auth/AccessToken.php`
  - [ ] Access token, refresh token, expires at
  - [ ] `isExpired(): bool`
- [ ] **5.1.3** Create `src/Auth/OAuthFlow.php`
  - [ ] `authorizationUrl(array $scopes, ?string $state = null): string`
  - [ ] `exchangeCode(string $code): AccessToken`
  - [ ] `refreshToken(string $refreshToken): AccessToken`
- [ ] **5.1.4** Create `src/Enums/Scope.php`
  - [ ] All OAuth scopes as enum cases

### 5.2 Webhooks

- [ ] **5.2.1** Create `src/Webhooks/WebhookSignature.php`
  - [ ] `verify(string $payload, string $signature, string $secret): bool`
  - [ ] `generate(string $payload, string $secret): string`
  - [ ] Timestamp tolerance validation
- [ ] **5.2.2** Create `src/Webhooks/WebhookPayload.php`
  - [ ] `static fromRequest(Request $request): static`
  - [ ] Event type enum property
  - [ ] Data array accessor
  - [ ] Timestamp
- [ ] **5.2.3** Create `src/Http/Middleware/VerifyWebhookSignature.php`
  - [ ] Verify signature header
  - [ ] Throw WebhookVerificationException on failure
- [ ] **5.2.4** Create `src/Exceptions/WebhookVerificationException.php`

### 5.3 Webhooks Resource

- [ ] **5.3.1** Create `src/Resources/Webhooks/WebhooksResource.php`
  - [ ] Standard CRUD operations
  - [ ] `test(int|string $id): bool`
  - [ ] `logs(int|string $id): Collection`
- [ ] **5.3.2** Create `src/Data/Webhook.php`
- [ ] **5.3.3** Create `src/Data/WebhookLog.php`
- [ ] **5.3.4** Create `src/Enums/WebhookEvent.php`
  - [ ] All webhook event types
- [ ] **5.3.5** Create `src/Enums/WebhookStatus.php`

---

## Phase 6: Communication & Documents

### 6.1 Messages Resource

- [ ] **6.1.1** Create `src/Resources/Messages/MessagesResource.php`
  - [ ] List with driver filter
  - [ ] Find by ID
  - [ ] `send(array $data): Message`
  - [ ] `broadcast(array $data): Collection`
- [ ] **6.1.2** Create `src/Data/Message.php`
- [ ] **6.1.3** Create `src/Enums/MessageDirection.php`

### 6.2 Documents Resource

- [ ] **6.2.1** Create `src/Resources/Documents/DocumentsResource.php`
  - [ ] List with filters
  - [ ] Find by ID
  - [ ] `upload(array $data): Document`
  - [ ] `download(int|string $id): string`
  - [ ] `updateStatus(int|string $id, DocumentStatus $status): Document`
  - [ ] Delete
- [ ] **6.2.2** Create `src/Data/Document.php`
- [ ] **6.2.3** Create `src/Data/DocumentImage.php`
- [ ] **6.2.4** Create `src/Enums/DocumentType.php`
- [ ] **6.2.5** Create `src/Enums/DocumentStatus.php`

---

## Phase 7: Fuel & Reporting

### 7.1 Fuel Purchases Resource

- [ ] **7.1.1** Create `src/Resources/FuelPurchases/FuelPurchasesResource.php`
  - [ ] Standard CRUD operations
  - [ ] Date range and vehicle filters
- [ ] **7.1.2** Create `src/Data/FuelPurchase.php`

### 7.2 IFTA Reports Resource

- [ ] **7.2.1** Create `src/Resources/IftaReports/IftaReportsResource.php`
  - [ ] `generate(array $params): IftaReport`
  - [ ] `list(array $params = []): LazyCollection`
- [ ] **7.2.2** Create `src/Data/IftaReport.php`
- [ ] **7.2.3** Create `src/Data/IftaJurisdiction.php`

### 7.3 Driver Performance Resource

- [ ] **7.3.1** Create `src/Resources/DriverPerformance/DriverPerformanceEventsResource.php`
  - [ ] List with filters
  - [ ] Find by ID
- [ ] **7.3.2** Create `src/Data/DriverPerformanceEvent.php`
- [ ] **7.3.3** Create `src/Enums/PerformanceEventType.php`
- [ ] **7.3.4** Create `src/Enums/EventSeverity.php`

### 7.4 Scorecard Resource

- [ ] **7.4.1** Create `src/Resources/Scorecard/ScorecardResource.php`
  - [ ] `forDriver(int|string $driverId, array $params = []): Scorecard`
  - [ ] `forFleet(array $params = []): Scorecard`
- [ ] **7.4.2** Create `src/Data/Scorecard.php`

### 7.5 Utilization Resource

- [ ] **7.5.1** Create `src/Resources/Utilization/UtilizationResource.php`
  - [ ] `forVehicle(int|string $vehicleId, array $params = []): UtilizationReport`
  - [ ] `forFleet(array $params = []): UtilizationReport`
  - [ ] `daily(array $params = []): Collection`
  - [ ] `summary(array $params = []): UtilizationReport`
- [ ] **7.5.2** Create `src/Data/UtilizationReport.php`
- [ ] **7.5.3** Create `src/Data/UtilizationDay.php`

---

## Phase 8: Time & Forms

### 8.1 Timecards Resource

- [ ] **8.1.1** Create `src/Resources/Timecards/TimecardsResource.php`
  - [ ] List with filters
  - [ ] Find by ID
  - [ ] Update
- [ ] **8.1.2** Create `src/Data/Timecard.php`
- [ ] **8.1.3** Create `src/Data/TimecardEntry.php`
- [ ] **8.1.4** Create `src/Enums/TimecardStatus.php`

### 8.2 Forms Resource

- [ ] **8.2.1** Create `src/Resources/Forms/FormsResource.php`
  - [ ] List available forms
- [ ] **8.2.2** Create `src/Data/Form.php`
- [ ] **8.2.3** Create `src/Data/FormField.php`
- [ ] **8.2.4** Create `src/Enums/FormFieldType.php`

### 8.3 Form Entries Resource

- [ ] **8.3.1** Create `src/Resources/Forms/FormEntriesResource.php`
  - [ ] List with filters
  - [ ] Find by ID
- [ ] **8.3.2** Create `src/Data/FormEntry.php`

### 8.4 Driving Periods Resource

- [ ] **8.4.1** Create `src/Resources/DrivingPeriods/DrivingPeriodsResource.php`
  - [ ] `list(array $params = []): LazyCollection`
  - [ ] `current(int|string $driverId): DrivingPeriod`
  - [ ] `history(int|string $driverId, array $params = []): Collection`
- [ ] **8.4.2** Create `src/Data/DrivingPeriod.php`

---

## Phase 9: Advanced Resources

### 9.1 Motive Card Resource

- [ ] **9.1.1** Create `src/Resources/MotiveCard/MotiveCardResource.php`
  - [ ] List cards
  - [ ] `transactions(int|string $cardId, array $params = []): Collection`
  - [ ] `limits(int|string $cardId): CardLimit`
- [ ] **9.1.2** Create `src/Data/MotiveCard.php`
- [ ] **9.1.3** Create `src/Data/CardTransaction.php`
- [ ] **9.1.4** Create `src/Data/CardLimit.php`
- [ ] **9.1.5** Create `src/Enums/CardTransactionType.php`

### 9.2 Freight Visibility Resource

- [ ] **9.2.1** Create `src/Resources/FreightVisibility/FreightVisibilityResource.php`
  - [ ] `shipments(array $params = []): LazyCollection`
  - [ ] `tracking(string $shipmentId): ShipmentTracking`
  - [ ] `eta(string $shipmentId): ShipmentEta`
- [ ] **9.2.2** Create `src/Data/Shipment.php`
- [ ] **9.2.3** Create `src/Data/ShipmentTracking.php`
- [ ] **9.2.4** Create `src/Data/ShipmentEta.php`
- [ ] **9.2.5** Create `src/Enums/ShipmentStatus.php`

### 9.3 Camera Resources

- [ ] **9.3.1** Create `src/Resources/Camera/CameraConnectionsResource.php`
  - [ ] List camera connections
- [ ] **9.3.2** Create `src/Resources/Camera/CameraControlResource.php`
  - [ ] `requestVideo(array $params): VideoRequest`
  - [ ] `getVideo(string $requestId): Video`
- [ ] **9.3.3** Create `src/Data/CameraConnection.php`
- [ ] **9.3.4** Create `src/Data/VideoRequest.php`
- [ ] **9.3.5** Create `src/Data/Video.php`
- [ ] **9.3.6** Create `src/Enums/CameraType.php`
- [ ] **9.3.7** Create `src/Enums/VideoStatus.php`

### 9.4 External IDs Resource

- [ ] **9.4.1** Create `src/Resources/ExternalIds/ExternalIdsResource.php`
  - [ ] `set(string $resourceType, int|string $resourceId, string $externalId): bool`
  - [ ] `get(string $resourceType, int|string $resourceId): ?string`
  - [ ] `delete(string $resourceType, int|string $resourceId): bool`
- [ ] **9.4.2** Create `src/Data/ExternalId.php`

### 9.5 Vehicle Gateways Resource

- [ ] **9.5.1** Create `src/Resources/VehicleGateways/VehicleGatewaysResource.php`
  - [ ] List gateways
- [ ] **9.5.2** Create `src/Data/VehicleGateway.php`

### 9.6 Reefer Activity Resource

- [ ] **9.6.1** Create `src/Resources/ReeferActivity/ReeferActivityResource.php`
  - [ ] `list(array $params = []): LazyCollection`
  - [ ] `forVehicle(int|string $vehicleId, array $params = []): Collection`
- [ ] **9.6.2** Create `src/Data/ReeferActivity.php`

---

## Phase 10: Testing Infrastructure

### 10.1 Testing Core

- [ ] **10.1.1** Create `src/Testing/MotiveFake.php`
  - [ ] Replace real client with fake
  - [ ] Configure fake responses
  - [ ] Track request history
  - [ ] Assertion methods
- [ ] **10.1.2** Create `src/Testing/FakeResponse.php`
  - [ ] `static json(array $data): static`
  - [ ] `static paginated(array $items, int $total, int $perPage): static`
  - [ ] `static error(int $status, array $body): static`
  - [ ] `static empty(): static`
- [ ] **10.1.3** Create `src/Testing/RequestHistory.php`
  - [ ] Store and query recorded requests
  - [ ] Assertion helpers

### 10.2 Factories

- [ ] **10.2.1** Create `src/Testing/Factories/Factory.php` (base class)
  - [ ] `make(array $attributes = []): DataTransferObject`
  - [ ] `count(int $count): static`
  - [ ] State methods
- [ ] **10.2.2** Create `src/Testing/Factories/VehicleFactory.php`
- [ ] **10.2.3** Create `src/Testing/Factories/UserFactory.php`
- [ ] **10.2.4** Create `src/Testing/Factories/DriverFactory.php`
- [ ] **10.2.5** Create `src/Testing/Factories/AssetFactory.php`
- [ ] **10.2.6** Create `src/Testing/Factories/HosLogFactory.php`
- [ ] **10.2.7** Create `src/Testing/Factories/HosAvailabilityFactory.php`
- [ ] **10.2.8** Create `src/Testing/Factories/DispatchFactory.php`
- [ ] **10.2.9** Create `src/Testing/Factories/LocationFactory.php`
- [ ] **10.2.10** Create `src/Testing/Factories/GeofenceFactory.php`
- [ ] **10.2.11** Create `src/Testing/Factories/DocumentFactory.php`
- [ ] **10.2.12** Create `src/Testing/Factories/MessageFactory.php`
- [ ] **10.2.13** Create `src/Testing/Factories/WebhookFactory.php`
- [ ] **10.2.14** Create `src/Testing/Factories/InspectionReportFactory.php`
- [ ] **10.2.15** Create `src/Testing/Factories/FuelPurchaseFactory.php`

### 10.3 Test Suite Setup

- [ ] **10.3.1** Create `tests/TestCase.php`
  - [ ] PHPUnit configuration
  - [ ] Helper methods
  - [ ] Test traits
- [ ] **10.3.2** Create `phpunit.xml` configuration

### 10.4 Unit Tests

- [ ] **10.4.1** Create `tests/Unit/ClientTest.php`
  - [ ] Test HTTP client configuration
  - [ ] Test request building
  - [ ] Test response parsing
- [ ] **10.4.2** Create `tests/Unit/PaginationTest.php`
  - [ ] Test paginated response
  - [ ] Test lazy pagination
  - [ ] Test cursor pagination
- [ ] **10.4.3** Create `tests/Unit/AuthenticationTest.php`
  - [ ] Test API key auth
  - [ ] Test OAuth auth
  - [ ] Test token refresh
- [ ] **10.4.4** Create `tests/Unit/WebhookSignatureTest.php`
  - [ ] Test signature verification
  - [ ] Test signature generation
  - [ ] Test timestamp tolerance
- [ ] **10.4.5** Create `tests/Unit/DataTransferObjectTest.php`
  - [ ] Test DTO hydration
  - [ ] Test nested relationships
  - [ ] Test enum casting
  - [ ] Test date casting

### 10.5 Feature Tests

- [ ] **10.5.1** Create `tests/Feature/VehiclesResourceTest.php`
- [ ] **10.5.2** Create `tests/Feature/UsersResourceTest.php`
- [ ] **10.5.3** Create `tests/Feature/HosResourceTest.php`
- [ ] **10.5.4** Create `tests/Feature/DispatchesResourceTest.php`
- [ ] **10.5.5** Create `tests/Feature/WebhooksResourceTest.php`
- [ ] **10.5.6** Create `tests/Feature/OAuthFlowTest.php`
- [ ] **10.5.7** Create `tests/Feature/MultiTenancyTest.php`

---

## Phase 11: Documentation & Polish

### 11.1 Documentation

- [ ] **11.1.1** Update `README.md` with complete API examples
- [ ] **11.1.2** Create `CHANGELOG.md`
- [ ] **11.1.3** Create `CONTRIBUTING.md`
- [ ] **11.1.4** Create `LICENSE.md` (MIT)

### 11.2 PHPDoc

- [ ] **11.2.1** Add PHPDoc to all public methods in MotiveManager
- [ ] **11.2.2** Add PHPDoc to all public methods in Resources
- [ ] **11.2.3** Add PHPDoc to all DTOs
- [ ] **11.2.4** Add PHPDoc to all Enums
- [ ] **11.2.5** Add PHPDoc to Testing classes

### 11.3 Code Quality

- [ ] **11.3.1** Run PHPStan at level 8 and fix all errors
- [ ] **11.3.2** Run Laravel Pint and fix all style issues
- [ ] **11.3.3** Ensure 100% test coverage on critical paths
- [ ] **11.3.4** Review all exception messages for clarity

### 11.4 Final Review

- [ ] **11.4.1** Review all resource method signatures for consistency
- [ ] **11.4.2** Review all DTO properties for completeness
- [ ] **11.4.3** Review all enum cases against API documentation
- [ ] **11.4.4** Test full integration with real Motive API
- [ ] **11.4.5** Performance testing with large datasets
- [ ] **11.4.6** Prepare v1.0.0 release

---

## Progress Summary

| Phase | Total Items | Completed | Progress |
|-------|-------------|-----------|----------|
| Phase 1: Foundation | 28 | 28 | 100% |
| Phase 2: Essential Resources | 14 | 14 | 100% |
| Phase 3: HOS & Compliance | 15 | 0 | 0% |
| Phase 4: Dispatch & Location | 15 | 0 | 0% |
| Phase 5: OAuth & Webhooks | 14 | 0 | 0% |
| Phase 6: Communication & Documents | 9 | 0 | 0% |
| Phase 7: Fuel & Reporting | 12 | 0 | 0% |
| Phase 8: Time & Forms | 10 | 0 | 0% |
| Phase 9: Advanced Resources | 18 | 0 | 0% |
| Phase 10: Testing Infrastructure | 25 | 0 | 0% |
| Phase 11: Documentation & Polish | 15 | 0 | 0% |
| **Total** | **175** | **42** | **24%** |

---

## Notes

- Each phase builds upon the previous phases
- Phase 1 (Foundation) must be completed before any other phase
- Phases 2-4 can be worked on in parallel after Phase 1
- Phase 5 (OAuth & Webhooks) can be started after Phase 1
- Phases 6-9 can be worked on in parallel after Phase 2
- Phase 10 (Testing) should be incrementally built alongside other phases
- Phase 11 (Documentation) is ongoing throughout development
