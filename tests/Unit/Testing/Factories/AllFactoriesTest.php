<?php

namespace Motive\Tests\Unit\Testing\Factories;

use Motive\Data\User;
use Motive\Data\Asset;
use Motive\Data\Driver;
use Motive\Data\HosLog;
use Motive\Data\Message;
use Motive\Data\Webhook;
use Motive\Data\Dispatch;
use Motive\Data\Document;
use Motive\Data\Geofence;
use Motive\Data\Location;
use Motive\Data\FuelPurchase;
use PHPUnit\Framework\TestCase;
use Motive\Data\HosAvailability;
use Motive\Data\InspectionReport;
use PHPUnit\Framework\Attributes\Test;
use Motive\Testing\Factories\UserFactory;
use Motive\Testing\Factories\AssetFactory;
use Motive\Testing\Factories\DriverFactory;
use Motive\Testing\Factories\HosLogFactory;
use Motive\Testing\Factories\MessageFactory;
use Motive\Testing\Factories\WebhookFactory;
use Motive\Testing\Factories\DispatchFactory;
use Motive\Testing\Factories\DocumentFactory;
use Motive\Testing\Factories\GeofenceFactory;
use Motive\Testing\Factories\LocationFactory;
use Motive\Testing\Factories\FuelPurchaseFactory;
use Motive\Testing\Factories\HosAvailabilityFactory;
use Motive\Testing\Factories\InspectionReportFactory;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AllFactoriesTest extends TestCase
{
    #[Test]
    public function asset_factory_assigned_state(): void
    {
        $asset = AssetFactory::new()->assignedTo(123)->make();

        $this->assertSame(123, $asset->vehicleId);
    }

    #[Test]
    public function asset_factory_creates_asset(): void
    {
        $asset = AssetFactory::new()->make();

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertIsString($asset->name);
    }

    #[Test]
    public function dispatch_factory_creates_dispatch(): void
    {
        $dispatch = DispatchFactory::new()->make();

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertIsString($dispatch->externalId);
    }

    #[Test]
    public function dispatch_factory_status_states(): void
    {
        $inProgress = DispatchFactory::new()->inProgress()->make();
        $completed = DispatchFactory::new()->completed()->make();

        $this->assertSame('in_progress', $inProgress->status->value);
        $this->assertSame('completed', $completed->status->value);
    }

    #[Test]
    public function document_factory_creates_document(): void
    {
        $document = DocumentFactory::new()->make();

        $this->assertInstanceOf(Document::class, $document);
        $this->assertNotNull($document->documentType);
    }

    #[Test]
    public function document_factory_status_states(): void
    {
        $approved = DocumentFactory::new()->approved()->make();
        $rejected = DocumentFactory::new()->rejected()->make();

        $this->assertSame('approved', $approved->status->value);
        $this->assertSame('rejected', $rejected->status->value);
    }

    #[Test]
    public function driver_factory_creates_driver(): void
    {
        $driver = DriverFactory::new()->make();

        $this->assertInstanceOf(Driver::class, $driver);
        $this->assertIsString($driver->licenseNumber);
    }

    #[Test]
    public function driver_factory_eld_exempt_state(): void
    {
        $driver = DriverFactory::new()->eldExempt()->make();

        $this->assertTrue($driver->eldExempt);
    }

    #[Test]
    public function fuel_purchase_factory_creates_purchase(): void
    {
        $purchase = FuelPurchaseFactory::new()->make();

        $this->assertInstanceOf(FuelPurchase::class, $purchase);
        $this->assertIsFloat($purchase->quantity);
        $this->assertIsFloat($purchase->totalCost);
    }

    #[Test]
    public function fuel_purchase_factory_fuel_type_states(): void
    {
        $diesel = FuelPurchaseFactory::new()->diesel()->make();
        $def = FuelPurchaseFactory::new()->def()->make();

        $this->assertSame('diesel', $diesel->fuelType);
        $this->assertSame('def', $def->fuelType);
    }

    #[Test]
    public function geofence_factory_creates_geofence(): void
    {
        $geofence = GeofenceFactory::new()->make();

        $this->assertInstanceOf(Geofence::class, $geofence);
        $this->assertSame('circle', $geofence->geofenceType->value);
    }

    #[Test]
    public function geofence_factory_polygon_state(): void
    {
        $coordinates = [
            ['latitude' => 37.7749, 'longitude' => -122.4194],
            ['latitude' => 37.7848, 'longitude' => -122.4094],
            ['latitude' => 37.7648, 'longitude' => -122.4294],
        ];

        $geofence = GeofenceFactory::new()->polygon($coordinates)->make();

        $this->assertSame('polygon', $geofence->geofenceType->value);
    }

    #[Test]
    public function hos_availability_factory_creates_availability(): void
    {
        $availability = HosAvailabilityFactory::new()->make();

        $this->assertInstanceOf(HosAvailability::class, $availability);
        $this->assertIsInt($availability->driveTimeRemaining);
    }

    #[Test]
    public function hos_availability_factory_out_of_time_state(): void
    {
        $availability = HosAvailabilityFactory::new()->outOfDriveTime()->make();

        $this->assertSame(0, $availability->driveTimeRemaining);
    }

    #[Test]
    public function hos_log_factory_creates_hos_log(): void
    {
        $log = HosLogFactory::new()->make();

        $this->assertInstanceOf(HosLog::class, $log);
        $this->assertNotNull($log->dutyStatus);
    }

    #[Test]
    public function hos_log_factory_status_states(): void
    {
        $driving = HosLogFactory::new()->driving()->make();
        $offDuty = HosLogFactory::new()->offDuty()->make();

        $this->assertSame('driving', $driving->dutyStatus->value);
        $this->assertSame('off_duty', $offDuty->dutyStatus->value);
    }

    #[Test]
    public function inspection_report_factory_creates_report(): void
    {
        $report = InspectionReportFactory::new()->make();

        $this->assertInstanceOf(InspectionReport::class, $report);
        $this->assertNotNull($report->inspectionType);
    }

    #[Test]
    public function inspection_report_factory_status_states(): void
    {
        $satisfactory = InspectionReportFactory::new()->satisfactory()->make();
        $failed = InspectionReportFactory::new()->failed()->make();

        $this->assertSame('satisfactory', $satisfactory->status->value);
        $this->assertSame('failed', $failed->status->value);
        $this->assertNotEmpty($failed->defects);
    }

    #[Test]
    public function inspection_report_factory_type_states(): void
    {
        $preTrip = InspectionReportFactory::new()->preTrip()->make();
        $postTrip = InspectionReportFactory::new()->postTrip()->make();

        $this->assertSame('pre_trip', $preTrip->inspectionType->value);
        $this->assertSame('post_trip', $postTrip->inspectionType->value);
    }

    #[Test]
    public function location_factory_at_coordinates(): void
    {
        $location = LocationFactory::new()->at(40.7128, -74.0060)->make();

        $this->assertSame(40.7128, $location->latitude);
        $this->assertSame(-74.0060, $location->longitude);
    }

    #[Test]
    public function location_factory_creates_location(): void
    {
        $location = LocationFactory::new()->make();

        $this->assertInstanceOf(Location::class, $location);
        $this->assertIsFloat($location->latitude);
        $this->assertIsFloat($location->longitude);
    }

    #[Test]
    public function message_factory_creates_message(): void
    {
        $message = MessageFactory::new()->make();

        $this->assertInstanceOf(Message::class, $message);
        $this->assertIsString($message->body);
    }

    #[Test]
    public function message_factory_direction_states(): void
    {
        $inbound = MessageFactory::new()->inbound()->make();
        $outbound = MessageFactory::new()->outbound()->make();

        $this->assertSame('inbound', $inbound->direction->value);
        $this->assertSame('outbound', $outbound->direction->value);
    }

    #[Test]
    public function user_factory_admin_state(): void
    {
        $user = UserFactory::new()->admin()->make();

        $this->assertSame('admin', $user->role->value);
    }

    #[Test]
    public function user_factory_creates_user(): void
    {
        $user = UserFactory::new()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertIsInt($user->id);
        $this->assertIsString($user->firstName);
    }

    #[Test]
    public function webhook_factory_creates_webhook(): void
    {
        $webhook = WebhookFactory::new()->make();

        $this->assertInstanceOf(Webhook::class, $webhook);
        $this->assertIsString($webhook->url);
    }

    #[Test]
    public function webhook_factory_subscribed_state(): void
    {
        $events = ['vehicle.location_updated', 'dispatch.created'];
        $webhook = WebhookFactory::new()->subscribedTo($events)->make();

        $this->assertCount(2, $webhook->events);
    }
}
