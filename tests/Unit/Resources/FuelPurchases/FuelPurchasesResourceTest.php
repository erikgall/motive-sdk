<?php

namespace Motive\Tests\Unit\Resources\FuelPurchases;

use Motive\Client\Response;
use Motive\Data\FuelPurchase;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\FuelPurchases\FuelPurchasesResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FuelPurchasesResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new FuelPurchasesResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/fuel_purchases', $resource->fullPath());
        $this->assertSame('/v1/fuel_purchases/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_fuel_purchase(): void
    {
        $fuelPurchaseData = [
            'id'         => 125,
            'company_id' => 456,
            'vehicle_id' => 789,
            'fuel_type'  => 'diesel',
            'quantity'   => 150.5,
            'total_cost' => 585.45,
        ];

        $response = $this->createMockResponse(['fuel_purchase' => $fuelPurchaseData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/fuel_purchases', ['fuel_purchase' => ['vehicle_id' => 789, 'fuel_type' => 'diesel', 'quantity' => 150.5, 'total_cost' => 585.45]])
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchase = $resource->create([
            'vehicle_id' => 789,
            'fuel_type'  => 'diesel',
            'quantity'   => 150.5,
            'total_cost' => 585.45,
        ]);

        $this->assertInstanceOf(FuelPurchase::class, $fuelPurchase);
        $this->assertSame(125, $fuelPurchase->id);
    }

    #[Test]
    public function it_deletes_fuel_purchase(): void
    {
        $response = $this->createStub(Response::class);
        $response->method('successful')->willReturn(true);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/fuel_purchases/123')
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_fuel_purchase_by_id(): void
    {
        $fuelPurchaseData = [
            'id'          => 123,
            'company_id'  => 456,
            'vehicle_id'  => 789,
            'fuel_type'   => 'diesel',
            'quantity'    => 150.5,
            'total_cost'  => 585.45,
            'vendor_name' => 'Pilot Travel Center',
        ];

        $response = $this->createMockResponse(['fuel_purchase' => $fuelPurchaseData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/fuel_purchases/123')
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchase = $resource->find(123);

        $this->assertInstanceOf(FuelPurchase::class, $fuelPurchase);
        $this->assertSame(123, $fuelPurchase->id);
        $this->assertSame('Pilot Travel Center', $fuelPurchase->vendorName);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new FuelPurchasesResource($this->createStub(MotiveClient::class));

        $this->assertSame('fuel_purchases', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new FuelPurchasesResource($this->createStub(MotiveClient::class));

        $this->assertSame('fuel_purchase', $resource->getResourceKey());
    }

    #[Test]
    public function it_lists_fuel_purchases(): void
    {
        $fuelPurchasesData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'fuel_type'  => 'diesel',
                'quantity'   => 150.5,
                'total_cost' => 585.45,
            ],
            [
                'id'         => 124,
                'company_id' => 456,
                'fuel_type'  => 'gasoline',
                'quantity'   => 25.0,
                'total_cost' => 75.00,
            ],
        ];

        $response = $this->createMockResponse([
            'fuel_purchases' => $fuelPurchasesData,
            'pagination'     => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $client = $this->createStub(MotiveClient::class);
        $client->method('get')->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchases = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $fuelPurchases);

        $fuelPurchasesArray = $fuelPurchases->all();
        $this->assertCount(2, $fuelPurchasesArray);
        $this->assertInstanceOf(FuelPurchase::class, $fuelPurchasesArray[0]);
        $this->assertSame(123, $fuelPurchasesArray[0]->id);
    }

    #[Test]
    public function it_lists_fuel_purchases_for_date_range(): void
    {
        $fuelPurchasesData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'fuel_type'  => 'diesel',
                'quantity'   => 150.5,
                'total_cost' => 585.45,
            ],
        ];

        $response = $this->createMockResponse([
            'fuel_purchases' => $fuelPurchasesData,
            'pagination'     => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/fuel_purchases', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31', 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchases = $resource->forDateRange('2024-01-01', '2024-01-31');

        $this->assertInstanceOf(LazyCollection::class, $fuelPurchases);
        $fuelPurchasesArray = $fuelPurchases->all();
        $this->assertCount(1, $fuelPurchasesArray);
    }

    #[Test]
    public function it_lists_fuel_purchases_for_vehicle(): void
    {
        $fuelPurchasesData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'vehicle_id' => 789,
                'fuel_type'  => 'diesel',
                'quantity'   => 150.5,
                'total_cost' => 585.45,
            ],
        ];

        $response = $this->createMockResponse([
            'fuel_purchases' => $fuelPurchasesData,
            'pagination'     => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/fuel_purchases', ['vehicle_id' => 789, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchases = $resource->forVehicle(789);

        $this->assertInstanceOf(LazyCollection::class, $fuelPurchases);
        $fuelPurchasesArray = $fuelPurchases->all();
        $this->assertCount(1, $fuelPurchasesArray);
        $this->assertSame(789, $fuelPurchasesArray[0]->vehicleId);
    }

    #[Test]
    public function it_updates_fuel_purchase(): void
    {
        $fuelPurchaseData = [
            'id'         => 123,
            'company_id' => 456,
            'fuel_type'  => 'diesel',
            'quantity'   => 175.0,
            'total_cost' => 680.75,
        ];

        $response = $this->createMockResponse(['fuel_purchase' => $fuelPurchaseData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/fuel_purchases/123', ['fuel_purchase' => ['quantity' => 175.0, 'total_cost' => 680.75]])
            ->willReturn($response);

        $resource = new FuelPurchasesResource($client);
        $fuelPurchase = $resource->update(123, [
            'quantity'   => 175.0,
            'total_cost' => 680.75,
        ]);

        $this->assertInstanceOf(FuelPurchase::class, $fuelPurchase);
        $this->assertSame(175.0, $fuelPurchase->quantity);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}
