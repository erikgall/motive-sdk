<?php

namespace Motive;

use InvalidArgumentException;
use Motive\Client\MotiveClient;
use Motive\Contracts\TokenStore;
use Motive\Contracts\Authenticator;
use Motive\Auth\ApiKeyAuthenticator;
use Motive\Resources\Forms\FormsResource;
use Motive\Resources\Users\UsersResource;
use Motive\Resources\Assets\AssetsResource;
use Motive\Resources\Groups\GroupsResource;
use Motive\Resources\Messages\MessagesResource;
use Motive\Resources\Vehicles\VehiclesResource;
use Motive\Resources\Webhooks\WebhooksResource;
use Motive\Resources\Companies\CompaniesResource;
use Motive\Resources\Documents\DocumentsResource;
use Motive\Resources\Geofences\GeofencesResource;
use Motive\Resources\Locations\LocationsResource;
use Motive\Resources\Scorecard\ScorecardResource;
use Motive\Resources\Timecards\TimecardsResource;
use Motive\Resources\Vehicles\FaultCodesResource;
use Motive\Resources\Camera\CameraControlResource;
use Motive\Resources\Dispatches\DispatchesResource;
use Motive\Resources\MotiveCard\MotiveCardResource;
use Motive\Resources\HoursOfService\HosLogsResource;
use Motive\Resources\ExternalIds\ExternalIdsResource;
use Motive\Resources\FormEntries\FormEntriesResource;
use Motive\Resources\IftaReports\IftaReportsResource;
use Motive\Resources\Utilization\UtilizationResource;
use Motive\Resources\Camera\CameraConnectionsResource;
use Motive\Resources\FuelPurchases\FuelPurchasesResource;
use Motive\Resources\HoursOfService\HosViolationsResource;
use Motive\Resources\DrivingPeriods\DrivingPeriodsResource;
use Motive\Resources\Inspections\InspectionReportsResource;
use Motive\Resources\ReeferActivity\ReeferActivityResource;
use Motive\Resources\HoursOfService\HosAvailabilityResource;
use Motive\Resources\VehicleGateways\VehicleGatewaysResource;
use Motive\Resources\FreightVisibility\FreightVisibilityResource;
use Motive\Resources\DriverPerformance\DriverPerformanceEventsResource;

/**
 * Manager for Motive API connections.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveManager
{
    protected ?Authenticator $authenticator = null;

    protected ?MotiveClient $client = null;

    /**
     * @var array<string, mixed>
     */
    protected array $contextHeaders = [];

    protected ?string $currentConnection = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config
    ) {
        $this->currentConnection = $config['default'] ?? 'default';
    }

    // ============================================
    // Resource Accessors
    // ============================================

    /**
     * Get the assets resource.
     */
    public function assets(): AssetsResource
    {
        return new AssetsResource($this->client());
    }

    /**
     * Get the camera connections resource.
     */
    public function cameraConnections(): CameraConnectionsResource
    {
        return new CameraConnectionsResource($this->client());
    }

    /**
     * Get the camera control resource.
     */
    public function cameraControl(): CameraControlResource
    {
        return new CameraControlResource($this->client());
    }

    /**
     * Get the HTTP client for making API requests.
     */
    public function client(): MotiveClient
    {
        if ($this->client !== null) {
            return $this->client;
        }

        $connectionConfig = $this->getConnectionConfig();
        $authenticator = $this->getAuthenticator();

        $this->client = new MotiveClient(
            baseUrl: $connectionConfig['base_url'],
            authenticator: $authenticator,
            timeout: $connectionConfig['timeout'] ?? 30,
            retryTimes: $connectionConfig['retry']['times'] ?? 3,
            retrySleep: $connectionConfig['retry']['sleep'] ?? 100
        );

        return $this->client;
    }

    /**
     * Get the companies resource.
     */
    public function companies(): CompaniesResource
    {
        return new CompaniesResource($this->client());
    }

    /**
     * Switch to a different connection.
     */
    public function connection(string $name): static
    {
        if (! isset($this->config['connections'][$name])) {
            throw new InvalidArgumentException("Connection [{$name}] not configured.");
        }

        $instance = clone $this;
        $instance->currentConnection = $name;
        $instance->authenticator = null;
        $instance->client = null;

        return $instance;
    }

    /**
     * Get the dispatches resource.
     */
    public function dispatches(): DispatchesResource
    {
        return new DispatchesResource($this->client());
    }

    /**
     * Get the documents resource.
     */
    public function documents(): DocumentsResource
    {
        return new DocumentsResource($this->client());
    }

    /**
     * Get the driver performance events resource.
     */
    public function driverPerformanceEvents(): DriverPerformanceEventsResource
    {
        return new DriverPerformanceEventsResource($this->client());
    }

    /**
     * Get the driving periods resource.
     */
    public function drivingPeriods(): DrivingPeriodsResource
    {
        return new DrivingPeriodsResource($this->client());
    }

    /**
     * Get the external IDs resource.
     */
    public function externalIds(): ExternalIdsResource
    {
        return new ExternalIdsResource($this->client());
    }

    /**
     * Get the fault codes resource.
     */
    public function faultCodes(): FaultCodesResource
    {
        return new FaultCodesResource($this->client());
    }

    /**
     * Get the form entries resource.
     */
    public function formEntries(): FormEntriesResource
    {
        return new FormEntriesResource($this->client());
    }

    /**
     * Get the forms resource.
     */
    public function forms(): FormsResource
    {
        return new FormsResource($this->client());
    }

    /**
     * Get the freight visibility resource.
     */
    public function freightVisibility(): FreightVisibilityResource
    {
        return new FreightVisibilityResource($this->client());
    }

    /**
     * Get the fuel purchases resource.
     */
    public function fuelPurchases(): FuelPurchasesResource
    {
        return new FuelPurchasesResource($this->client());
    }

    /**
     * Get the geofences resource.
     */
    public function geofences(): GeofencesResource
    {
        return new GeofencesResource($this->client());
    }

    /**
     * Get the authenticator for the current connection.
     */
    public function getAuthenticator(): Authenticator
    {
        if ($this->authenticator !== null) {
            return $this->authenticator;
        }

        $connectionConfig = $this->getConnectionConfig();
        $driver = $connectionConfig['auth_driver'] ?? 'api_key';

        $this->authenticator = match ($driver) {
            'api_key' => $this->createApiKeyAuthenticator($connectionConfig),
            'oauth'   => throw new InvalidArgumentException('OAuth authenticator requires explicit configuration via withOAuth()'),
            default   => throw new InvalidArgumentException("Unknown auth driver: {$driver}"),
        };

        return $this->authenticator;
    }

    /**
     * Get the current connection name.
     */
    public function getCurrentConnection(): string
    {
        return $this->currentConnection;
    }

    /**
     * Get the groups resource.
     */
    public function groups(): GroupsResource
    {
        return new GroupsResource($this->client());
    }

    /**
     * Get the HOS availability resource.
     */
    public function hosAvailability(): HosAvailabilityResource
    {
        return new HosAvailabilityResource($this->client());
    }

    /**
     * Get the HOS logs resource.
     */
    public function hosLogs(): HosLogsResource
    {
        return new HosLogsResource($this->client());
    }

    /**
     * Get the HOS violations resource.
     */
    public function hosViolations(): HosViolationsResource
    {
        return new HosViolationsResource($this->client());
    }

    /**
     * Get the IFTA reports resource.
     */
    public function iftaReports(): IftaReportsResource
    {
        return new IftaReportsResource($this->client());
    }

    /**
     * Get the inspection reports resource.
     */
    public function inspectionReports(): InspectionReportsResource
    {
        return new InspectionReportsResource($this->client());
    }

    /**
     * Get the locations resource.
     */
    public function locations(): LocationsResource
    {
        return new LocationsResource($this->client());
    }

    /**
     * Get the messages resource.
     */
    public function messages(): MessagesResource
    {
        return new MessagesResource($this->client());
    }

    /**
     * Get the Motive card resource.
     */
    public function motiveCard(): MotiveCardResource
    {
        return new MotiveCardResource($this->client());
    }

    /**
     * Get the reefer activity resource.
     */
    public function reeferActivity(): ReeferActivityResource
    {
        return new ReeferActivityResource($this->client());
    }

    /**
     * Get the scorecard resource.
     */
    public function scorecard(): ScorecardResource
    {
        return new ScorecardResource($this->client());
    }

    /**
     * Get the timecards resource.
     */
    public function timecards(): TimecardsResource
    {
        return new TimecardsResource($this->client());
    }

    /**
     * Get the users resource.
     */
    public function users(): UsersResource
    {
        return new UsersResource($this->client());
    }

    /**
     * Get the utilization resource.
     */
    public function utilization(): UtilizationResource
    {
        return new UtilizationResource($this->client());
    }

    /**
     * Get the vehicle gateways resource.
     */
    public function vehicleGateways(): VehicleGatewaysResource
    {
        return new VehicleGatewaysResource($this->client());
    }

    /**
     * Get the vehicles resource.
     */
    public function vehicles(): VehiclesResource
    {
        return new VehiclesResource($this->client());
    }

    /**
     * Get the webhooks resource.
     */
    public function webhooks(): WebhooksResource
    {
        return new WebhooksResource($this->client());
    }

    /**
     * Create a new instance with a custom API key.
     */
    public function withApiKey(string $apiKey): static
    {
        $instance = clone $this;
        $instance->authenticator = new ApiKeyAuthenticator($apiKey);
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with a custom authenticator.
     */
    public function withAuthenticator(Authenticator $authenticator): static
    {
        $instance = clone $this;
        $instance->authenticator = $authenticator;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with metric units enabled.
     */
    public function withMetricUnits(bool $enabled = true): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-Metric-Units'] = $enabled ? 'true' : 'false';
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with OAuth tokens.
     */
    public function withOAuth(string $accessToken, ?string $refreshToken = null, ?TokenStore $tokenStore = null): static
    {
        // This will be implemented in Phase 5 when OAuth is added
        throw new InvalidArgumentException('OAuth support is not yet implemented');
    }

    /**
     * Create a new instance with a specific timezone.
     */
    public function withTimezone(string $timezone): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-Timezone'] = $timezone;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create a new instance with a specific user ID for auditing.
     */
    public function withUserId(int|string $userId): static
    {
        $instance = clone $this;
        $instance->contextHeaders['X-User-Id'] = (string) $userId;
        $instance->client = null;

        return $instance;
    }

    /**
     * Create an API key authenticator from the connection config.
     *
     * @param  array<string, mixed>  $config
     */
    protected function createApiKeyAuthenticator(array $config): ApiKeyAuthenticator
    {
        $apiKey = $config['api_key'] ?? null;

        if ($apiKey === null || $apiKey === '') {
            throw new InvalidArgumentException('API key is required for api_key authentication');
        }

        return new ApiKeyAuthenticator($apiKey);
    }

    /**
     * Get the configuration for the current connection.
     *
     * @return array<string, mixed>
     */
    protected function getConnectionConfig(): array
    {
        $connections = $this->config['connections'] ?? [];
        $connection = $connections[$this->currentConnection] ?? null;

        if ($connection === null) {
            throw new InvalidArgumentException("Connection [{$this->currentConnection}] not configured.");
        }

        return $connection;
    }
}
