<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\WebhookEvent;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookEventTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $event = WebhookEvent::from('vehicle.location_updated');

        $this->assertSame(WebhookEvent::VehicleLocationUpdated, $event);
    }

    #[Test]
    public function it_has_dispatch_created_event(): void
    {
        $this->assertSame('dispatch.created', WebhookEvent::DispatchCreated->value);
    }

    #[Test]
    public function it_has_dispatch_updated_event(): void
    {
        $this->assertSame('dispatch.updated', WebhookEvent::DispatchUpdated->value);
    }

    #[Test]
    public function it_has_driver_hos_status_changed_event(): void
    {
        $this->assertSame('driver.hos_status_changed', WebhookEvent::DriverHosStatusChanged->value);
    }

    #[Test]
    public function it_has_driver_hos_violation_event(): void
    {
        $this->assertSame('driver.hos_violation', WebhookEvent::DriverHosViolation->value);
    }

    #[Test]
    public function it_has_geofence_entered_event(): void
    {
        $this->assertSame('geofence.entered', WebhookEvent::GeofenceEntered->value);
    }

    #[Test]
    public function it_has_geofence_exited_event(): void
    {
        $this->assertSame('geofence.exited', WebhookEvent::GeofenceExited->value);
    }

    #[Test]
    public function it_has_inspection_submitted_event(): void
    {
        $this->assertSame('inspection.submitted', WebhookEvent::InspectionSubmitted->value);
    }

    #[Test]
    public function it_has_vehicle_created_event(): void
    {
        $this->assertSame('vehicle.created', WebhookEvent::VehicleCreated->value);
    }

    #[Test]
    public function it_has_vehicle_location_updated_event(): void
    {
        $this->assertSame('vehicle.location_updated', WebhookEvent::VehicleLocationUpdated->value);
    }

    #[Test]
    public function it_has_vehicle_updated_event(): void
    {
        $this->assertSame('vehicle.updated', WebhookEvent::VehicleUpdated->value);
    }
}
