<?php

namespace Motive\Tests\Unit\Testing\Factories;

use Motive\Data\Vehicle;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Testing\Factories\VehicleFactory;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FactoryTest extends TestCase
{
    #[Test]
    public function it_can_chain_states(): void
    {
        $vehicle = VehicleFactory::new()
            ->inactive()
            ->withVin('1HGBH41JXMN109186')
            ->make();

        $this->assertSame('inactive', $vehicle->status->value);
        $this->assertSame('1HGBH41JXMN109186', $vehicle->vin);
    }

    #[Test]
    public function it_can_create_multiple_raw_arrays(): void
    {
        $data = VehicleFactory::new()->count(3)->raw();

        $this->assertIsArray($data);
        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertArrayHasKey('id', $item);
        }
    }

    #[Test]
    public function it_can_create_raw_array(): void
    {
        $data = VehicleFactory::new()->raw();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('number', $data);
        $this->assertArrayHasKey('company_id', $data);
    }

    #[Test]
    public function it_can_make_multiple_instances(): void
    {
        $vehicles = VehicleFactory::new()->count(3)->make();

        $this->assertIsArray($vehicles);
        $this->assertCount(3, $vehicles);

        foreach ($vehicles as $vehicle) {
            $this->assertInstanceOf(Vehicle::class, $vehicle);
        }
    }

    #[Test]
    public function it_can_make_single_instance(): void
    {
        $vehicle = VehicleFactory::new()->make();

        $this->assertInstanceOf(Vehicle::class, $vehicle);
        $this->assertIsInt($vehicle->id);
        $this->assertIsString($vehicle->number);
    }

    #[Test]
    public function it_can_override_attributes(): void
    {
        $vehicle = VehicleFactory::new()->make([
            'number' => 'CUSTOM-001',
            'make'   => 'Tesla',
        ]);

        $this->assertSame('CUSTOM-001', $vehicle->number);
        $this->assertSame('Tesla', $vehicle->make);
    }

    #[Test]
    public function it_can_sequence_values(): void
    {
        $vehicles = VehicleFactory::new()
            ->sequence(
                ['make' => 'Ford'],
                ['make' => 'Chevy'],
                ['make' => 'Dodge']
            )
            ->count(6)
            ->make();

        $this->assertSame('Ford', $vehicles[0]->make);
        $this->assertSame('Chevy', $vehicles[1]->make);
        $this->assertSame('Dodge', $vehicles[2]->make);
        $this->assertSame('Ford', $vehicles[3]->make);
        $this->assertSame('Chevy', $vehicles[4]->make);
        $this->assertSame('Dodge', $vehicles[5]->make);
    }

    #[Test]
    public function it_can_use_state_modifiers(): void
    {
        $vehicle = VehicleFactory::new()->inactive()->make();

        $this->assertSame('inactive', $vehicle->status->value);
    }

    #[Test]
    public function it_generates_unique_ids(): void
    {
        $vehicles = VehicleFactory::new()->count(5)->make();

        $ids = array_map(fn (Vehicle $v) => $v->id, $vehicles);

        $this->assertCount(5, array_unique($ids));
    }
}
