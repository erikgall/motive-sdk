<?php

namespace Motive\Tests\Feature;

use Motive\Data\Dispatch;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Motive\Enums\DispatchStatus;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Dispatches\DispatchesResource;

/**
 * Feature tests for DispatchesResource integration.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DispatchesResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_creates_dispatch_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches' => Http::response([
                'dispatch' => [
                    'id'          => 1,
                    'company_id'  => 100,
                    'external_id' => 'LOAD-12345',
                    'status'      => 'pending',
                    'driver_id'   => 50,
                    'vehicle_id'  => 25,
                    'stops'       => [],
                    'created_at'  => '2024-01-15T10:00:00Z',
                    'updated_at'  => '2024-01-15T10:00:00Z',
                ],
            ], 201),
        ]);

        $dispatch = Motive::dispatches()->create([
            'external_id' => 'LOAD-12345',
            'driver_id'   => 50,
            'vehicle_id'  => 25,
        ]);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertEquals(1, $dispatch->id);
        $this->assertEquals('LOAD-12345', $dispatch->externalId);
        $this->assertEquals(DispatchStatus::Pending, $dispatch->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/dispatches')
                && $request->method() === 'POST';
        });
    }

    #[Test]
    public function it_deletes_dispatch_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches/1' => Http::response([], 204),
        ]);

        $result = Motive::dispatches()->delete(1);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_dispatch_by_id_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches/1' => Http::response([
                'dispatch' => [
                    'id'          => 1,
                    'company_id'  => 100,
                    'external_id' => 'LOAD-12345',
                    'status'      => 'in_progress',
                    'driver_id'   => 50,
                    'vehicle_id'  => 25,
                    'stops'       => [
                        [
                            'id'          => 1,
                            'dispatch_id' => 1,
                            'stop_type'   => 'pickup',
                            'name'        => 'Warehouse A',
                            'address'     => '123 Main St',
                        ],
                        [
                            'id'          => 2,
                            'dispatch_id' => 1,
                            'stop_type'   => 'delivery',
                            'name'        => 'Customer B',
                            'address'     => '456 Oak Ave',
                        ],
                    ],
                ],
            ], 200),
        ]);

        $dispatch = Motive::dispatches()->find(1);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertEquals(DispatchStatus::InProgress, $dispatch->status);
        $this->assertCount(2, $dispatch->stops);
    }

    #[Test]
    public function it_gets_dispatches_resource_from_manager(): void
    {
        $resource = Motive::dispatches();

        $this->assertInstanceOf(DispatchesResource::class, $resource);
    }

    #[Test]
    public function it_lists_dispatches_by_status_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches/status/pending' => Http::response([
                'dispatches' => [
                    [
                        'id'          => 1,
                        'company_id'  => 100,
                        'external_id' => 'LOAD-12345',
                        'status'      => 'pending',
                        'driver_id'   => 50,
                        'vehicle_id'  => 25,
                    ],
                    [
                        'id'          => 2,
                        'company_id'  => 100,
                        'external_id' => 'LOAD-12346',
                        'status'      => 'pending',
                        'driver_id'   => 51,
                        'vehicle_id'  => 26,
                    ],
                ],
            ], 200),
        ]);

        $dispatches = Motive::dispatches()->byStatus('pending');

        $this->assertCount(2, $dispatches);
        $this->assertInstanceOf(Dispatch::class, $dispatches[0]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v1/dispatches/status/pending');
        });
    }

    #[Test]
    public function it_lists_dispatches_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches*' => Http::response([
                'dispatches' => [
                    [
                        'id'          => 1,
                        'company_id'  => 100,
                        'external_id' => 'LOAD-12345',
                        'status'      => 'pending',
                        'driver_id'   => 50,
                        'vehicle_id'  => 25,
                    ],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 1,
                ],
            ], 200),
        ]);

        $dispatches = Motive::dispatches()->list();

        $this->assertCount(1, iterator_to_array($dispatches));
    }

    #[Test]
    public function it_updates_dispatch_through_manager(): void
    {
        Http::fake([
            'api.gomotive.com/v1/dispatches/1' => Http::response([
                'dispatch' => [
                    'id'          => 1,
                    'company_id'  => 100,
                    'external_id' => 'LOAD-12345',
                    'status'      => 'completed',
                    'driver_id'   => 50,
                    'vehicle_id'  => 25,
                ],
            ], 200),
        ]);

        $dispatch = Motive::dispatches()->update(1, ['status' => 'completed']);

        $this->assertInstanceOf(Dispatch::class, $dispatch);
        $this->assertEquals(DispatchStatus::Completed, $dispatch->status);
    }
}
