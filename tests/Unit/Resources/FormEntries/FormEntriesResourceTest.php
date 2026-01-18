<?php

namespace Motive\Tests\Unit\Resources\FormEntries;

use Motive\Data\FormEntry;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\FormEntries\FormEntriesResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormEntriesResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new FormEntriesResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/form_entries', $resource->fullPath());
        $this->assertSame('/v1/form_entries/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_a_form_entry(): void
    {
        $data = [
            'form_id'      => 100,
            'driver_id'    => 200,
            'field_values' => [
                ['field_id' => 1, 'value' => 'Test value'],
            ],
        ];

        $entryData = [
            'id'           => 456,
            'form_id'      => 100,
            'driver_id'    => 200,
            'field_values' => [
                ['field_id' => 1, 'value' => 'Test value'],
            ],
        ];

        $response = $this->createMockResponse(['form_entry' => $entryData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/form_entries', ['form_entry' => $data])
            ->willReturn($response);

        $resource = new FormEntriesResource($client);
        $entry = $resource->create($data);

        $this->assertInstanceOf(FormEntry::class, $entry);
        $this->assertSame(456, $entry->id);
    }

    #[Test]
    public function it_finds_a_form_entry(): void
    {
        $entryData = [
            'id'        => 123,
            'form_id'   => 100,
            'driver_id' => 200,
        ];

        $response = $this->createMockResponse(['form_entry' => $entryData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/form_entries/123')
            ->willReturn($response);

        $resource = new FormEntriesResource($client);
        $entry = $resource->find(123);

        $this->assertInstanceOf(FormEntry::class, $entry);
        $this->assertSame(123, $entry->id);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new FormEntriesResource($this->createStub(MotiveClient::class));

        $this->assertSame('form_entries', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new FormEntriesResource($this->createStub(MotiveClient::class));

        $this->assertSame('form_entry', $resource->getResourceKey());
    }

    #[Test]
    public function it_lists_entries_for_driver(): void
    {
        $entriesData = [
            [
                'id'        => 1,
                'form_id'   => 100,
                'driver_id' => 200,
            ],
        ];

        $response = $this->createMockResponse([
            'form_entries' => $entriesData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/form_entries', ['driver_id' => 200, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new FormEntriesResource($client);
        $entries = $resource->forDriver(200);

        $this->assertInstanceOf(LazyCollection::class, $entries);
        $entriesArray = $entries->all();
        $this->assertCount(1, $entriesArray);
        $this->assertSame(200, $entriesArray[0]->driverId);
    }

    #[Test]
    public function it_lists_entries_for_form(): void
    {
        $entriesData = [
            [
                'id'        => 1,
                'form_id'   => 100,
                'driver_id' => 200,
            ],
        ];

        $response = $this->createMockResponse([
            'form_entries' => $entriesData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/form_entries', ['form_id' => 100, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new FormEntriesResource($client);
        $entries = $resource->forForm(100);

        $this->assertInstanceOf(LazyCollection::class, $entries);
        $entriesArray = $entries->all();
        $this->assertCount(1, $entriesArray);
        $this->assertSame(100, $entriesArray[0]->formId);
    }

    #[Test]
    public function it_lists_form_entries(): void
    {
        $entriesData = [
            [
                'id'        => 1,
                'form_id'   => 100,
                'driver_id' => 200,
            ],
            [
                'id'        => 2,
                'form_id'   => 100,
                'driver_id' => 201,
            ],
        ];

        $response = $this->createMockResponse([
            'form_entries' => $entriesData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $client = $this->createStub(MotiveClient::class);
        $client->method('get')->willReturn($response);

        $resource = new FormEntriesResource($client);
        $entries = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $entries);

        $entriesArray = $entries->all();
        $this->assertCount(2, $entriesArray);
        $this->assertInstanceOf(FormEntry::class, $entriesArray[0]);
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
