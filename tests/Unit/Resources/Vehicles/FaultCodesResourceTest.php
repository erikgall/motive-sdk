<?php

namespace Motive\Tests\Unit\Resources\Vehicles;

use Motive\Data\FaultCode;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Vehicles\FaultCodesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FaultCodesResourceTest extends TestCase
{
    #[Test]
    public function it_finds_fault_code_by_id(): void
    {
        $faultCodeData = [
            'id'         => 123,
            'vehicle_id' => 456,
            'code'       => 'P0300',
            'source'     => 'engine',
        ];

        $response = $this->createMockResponse(['fault_code' => $faultCodeData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/fault_codes/123')
            ->willReturn($response);

        $resource = new FaultCodesResource($client);
        $faultCode = $resource->find(123);

        $this->assertInstanceOf(FaultCode::class, $faultCode);
        $this->assertSame(123, $faultCode->id);
        $this->assertSame(456, $faultCode->vehicleId);
        $this->assertSame('P0300', $faultCode->code);
    }

    #[Test]
    public function it_gets_fault_codes_for_vehicle(): void
    {
        $faultCodeData = [
            'id'         => 123,
            'vehicle_id' => 456,
            'code'       => 'P0300',
        ];

        $response = $this->createMockResponse(['fault_codes' => [$faultCodeData]]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/fault_codes/vehicle/456', [])
            ->willReturn($response);

        $resource = new FaultCodesResource($client);
        $faultCodes = $resource->forVehicle(456);

        $this->assertIsArray($faultCodes);
        $this->assertCount(1, $faultCodes);
        $this->assertInstanceOf(FaultCode::class, $faultCodes[0]);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new FaultCodesResource($this->createStub(MotiveClient::class));

        $this->assertSame('fault_codes', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new FaultCodesResource($this->createStub(MotiveClient::class));

        $this->assertSame('fault_code', $resource->getResourceKey());
    }

    #[Test]
    public function it_marks_fault_code_as_resolved(): void
    {
        $response = $this->createMockResponse(['fault_code' => [
            'id'          => 123,
            'vehicle_id'  => 456,
            'code'        => 'P0300',
            'resolved'    => true,
            'resolved_at' => '2024-01-15T10:00:00Z',
        ]]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/fault_codes/123/resolve', [])
            ->willReturn($response);

        $resource = new FaultCodesResource($client);
        $faultCode = $resource->resolve(123);

        $this->assertInstanceOf(FaultCode::class, $faultCode);
        $this->assertTrue($faultCode->resolved);
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
