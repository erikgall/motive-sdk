<?php

namespace Motive\Tests\Unit\Resources;

use Motive\Resources\Resource;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Data\DataTransferObject;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ResourceTest extends TestCase
{
    #[Test]
    public function it_constructs_full_path_with_api_version(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $resource = new TestResource($client);

        $this->assertEquals('/v1/vehicles', $resource->fullPath());
    }

    #[Test]
    public function it_constructs_full_path_with_suffix(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $resource = new TestResource($client);

        $this->assertEquals('/v1/vehicles/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_returns_base_path(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $resource = new TestResource($client);

        $this->assertEquals('vehicles', $resource->getBasePath());
    }

    #[Test]
    public function it_returns_plural_resource_key(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $resource = new TestResource($client);

        $this->assertEquals('vehicles', $resource->getPluralResourceKey());
    }

    #[Test]
    public function it_returns_resource_key(): void
    {
        $client = $this->createMock(MotiveClient::class);

        $resource = new TestResource($client);

        $this->assertEquals('vehicle', $resource->getResourceKey());
    }
}

class TestResource extends Resource
{
    protected string $apiVersion = '1';

    protected function basePath(): string
    {
        return 'vehicles';
    }

    protected function dtoClass(): string
    {
        return DataTransferObject::class;
    }

    protected function resourceKey(): string
    {
        return 'vehicle';
    }
}
