<?php

namespace Motive\Tests\Unit\Resources\Concerns;

use Carbon\CarbonImmutable;
use Motive\Client\Response;
use Motive\Resources\Resource;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Data\DataTransferObject;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Pagination\PaginatedResponse;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class HasCrudOperationsTest extends TestCase
{
    #[Test]
    public function it_creates_resource(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'item' => ['id' => 1, 'name' => 'New Item', 'created_at' => '2024-01-17T10:00:00Z'],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->once())
            ->method('post')
            ->with('/v1/items', ['item' => ['name' => 'New Item']])
            ->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->create(['name' => 'New Item']);

        $this->assertInstanceOf(TestItemDto::class, $result);
        $this->assertEquals('New Item', $result->name);
    }

    #[Test]
    public function it_deletes_resource(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('successful')->willReturn(true);

        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/items/1')
            ->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_resource_by_id(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'item' => ['id' => 1, 'name' => 'Test 1', 'created_at' => '2024-01-17T10:00:00Z'],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->once())
            ->method('get')
            ->with('/v1/items/1')
            ->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->find(1);

        $this->assertInstanceOf(TestItemDto::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_lists_resources_with_lazy_collection(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'items' => [
                    ['id' => 1, 'name' => 'Test 1', 'created_at' => '2024-01-17T10:00:00Z'],
                    ['id' => 2, 'name' => 'Test 2', 'created_at' => '2024-01-17T11:00:00Z'],
                ],
                'pagination' => [
                    'total'        => 2,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->method('get')->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $result);
        $items = $result->all();
        $this->assertCount(2, $items);
        $this->assertInstanceOf(TestItemDto::class, $items[0]);
    }

    #[Test]
    public function it_paginates_resources(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'items' => [
                    ['id' => 1, 'name' => 'Test 1', 'created_at' => '2024-01-17T10:00:00Z'],
                ],
                'pagination' => [
                    'total'        => 50,
                    'per_page'     => 25,
                    'current_page' => 1,
                ],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->method('get')->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->paginate(1, 25);

        $this->assertInstanceOf(PaginatedResponse::class, $result);
        $this->assertEquals(50, $result->total());
    }

    #[Test]
    public function it_updates_resource(): void
    {
        $client = $this->createMock(MotiveClient::class);
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturnCallback(function (?string $key = null) {
            $data = [
                'item' => ['id' => 1, 'name' => 'Updated Item', 'created_at' => '2024-01-17T10:00:00Z'],
            ];

            return $key === null ? $data : ($data[$key] ?? null);
        });

        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/items/1', ['item' => ['name' => 'Updated Item']])
            ->willReturn($response);

        $resource = new CrudTestResource($client);
        $result = $resource->update(1, ['name' => 'Updated Item']);

        $this->assertInstanceOf(TestItemDto::class, $result);
        $this->assertEquals('Updated Item', $result->name);
    }
}

/**
 * Test DTO for CRUD operations.
 *
 * @property int $id
 * @property string $name
 * @property CarbonImmutable|null $createdAt
 */
class TestItemDto extends DataTransferObject
{
    /**
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'createdAt' => CarbonImmutable::class,
    ];
}

class CrudTestResource extends Resource
{
    use HasCrudOperations;

    protected string $apiVersion = '1';

    protected function basePath(): string
    {
        return 'items';
    }

    protected function dtoClass(): string
    {
        return TestItemDto::class;
    }

    protected function resourceKey(): string
    {
        return 'item';
    }
}
