<?php

namespace Motive\Tests\Unit\Resources\Assets;

use Motive\Data\Asset;
use Motive\Client\Response;
use Motive\Enums\AssetType;
use Motive\Enums\AssetStatus;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Assets\AssetsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AssetsResourceTest extends TestCase
{
    private MotiveClient $client;

    private AssetsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new AssetsResource($this->client);
    }

    #[Test]
    public function it_assigns_asset_to_vehicle(): void
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('status')->willReturn(200);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/assets/123/assign', ['vehicle_id' => 456])
            ->willReturn($response);

        $result = $this->resource->assignToVehicle(123, 456);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_creates_asset(): void
    {
        $assetData = [
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'New Trailer',
            'asset_type' => 'trailer',
        ];

        $response = $this->createMockResponse(['asset' => $assetData], 201);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/assets', ['asset' => [
                'name'       => 'New Trailer',
                'asset_type' => 'trailer',
            ]])
            ->willReturn($response);

        $asset = $this->resource->create([
            'name'       => 'New Trailer',
            'asset_type' => 'trailer',
        ]);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame('New Trailer', $asset->name);
    }

    #[Test]
    public function it_deletes_asset(): void
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('status')->willReturn(204);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $this->client->expects($this->once())
            ->method('delete')
            ->with('/v1/assets/123')
            ->willReturn($response);

        $result = $this->resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_asset_by_id(): void
    {
        $assetData = [
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Trailer 001',
            'asset_type' => 'trailer',
            'status'     => 'active',
        ];

        $response = $this->createMockResponse(['asset' => $assetData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/assets/123')
            ->willReturn($response);

        $asset = $this->resource->find(123);

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame(123, $asset->id);
        $this->assertSame('Trailer 001', $asset->name);
        $this->assertSame(AssetType::Trailer, $asset->assetType);
        $this->assertSame(AssetStatus::Active, $asset->status);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('assets', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('asset', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_unassigns_asset_from_vehicle(): void
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('status')->willReturn(200);
        $httpResponse->method('successful')->willReturn(true);

        $response = new Response($httpResponse);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/assets/123/unassign')
            ->willReturn($response);

        $result = $this->resource->unassignFromVehicle(123);

        $this->assertTrue($result);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}
