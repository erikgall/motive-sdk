<?php

namespace Motive\Tests\Feature;

use Motive\Data\Vehicle;
use Motive\Facades\Motive;
use Motive\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

/**
 * Performance tests for large dataset handling.
 *
 * These tests verify that the SDK handles large datasets efficiently
 * using lazy pagination and memory-efficient iteration patterns.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class PerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Http::preventStrayRequests();
    }

    #[Test]
    public function it_handles_batched_processing(): void
    {
        // Simulate processing items in batches
        $perPage = 25;
        $total = 100; // 4 pages

        Http::fake(function ($request) use ($perPage, $total) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
            $pageNo = isset($query['page_no']) ? (int) $query['page_no'] : 1;

            $vehicles = [];
            $startId = ($pageNo - 1) * $perPage + 1;
            for ($i = 0; $i < $perPage && ($startId + $i) <= $total; $i++) {
                $id = $startId + $i;
                $vehicles[] = [
                    'id'         => $id,
                    'company_id' => 100,
                    'number'     => sprintf('TRUCK-%04d', $id),
                    'status'     => 'active',
                ];
            }

            return Http::response([
                'vehicles'   => $vehicles,
                'pagination' => [
                    'per_page' => $perPage,
                    'page_no'  => $pageNo,
                    'total'    => $total,
                ],
            ], 200);
        });

        $vehicles = Motive::vehicles()->list(['per_page' => $perPage]);

        // Process in chunks of 10
        $chunks = [];
        $currentChunk = [];

        foreach ($vehicles as $vehicle) {
            $currentChunk[] = $vehicle;
            if (count($currentChunk) >= 10) {
                $chunks[] = $currentChunk;
                $currentChunk = [];
            }
        }

        if (! empty($currentChunk)) {
            $chunks[] = $currentChunk;
        }

        // We should have 10 chunks of 10 items each
        $this->assertCount(10, $chunks);
        foreach ($chunks as $chunk) {
            $this->assertCount(10, $chunk);
        }
    }

    #[Test]
    public function it_handles_concurrent_resource_iteration(): void
    {
        // Simulate both vehicles and users pagination
        $vehiclesResponseSequence = Http::sequence()
            ->push([
                'vehicles' => [
                    ['id' => 1, 'company_id' => 100, 'number' => 'V-001', 'status' => 'active'],
                    ['id' => 2, 'company_id' => 100, 'number' => 'V-002', 'status' => 'active'],
                ],
                'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
            ], 200);

        $usersResponseSequence = Http::sequence()
            ->push([
                'users' => [
                    ['id' => 1, 'email' => 'user1@test.com', 'first_name' => 'John', 'last_name' => 'Doe', 'status' => 'active'],
                    ['id' => 2, 'email' => 'user2@test.com', 'first_name' => 'Jane', 'last_name' => 'Smith', 'status' => 'active'],
                    ['id' => 3, 'email' => 'user3@test.com', 'first_name' => 'Bob', 'last_name' => 'Wilson', 'status' => 'active'],
                ],
                'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 3],
            ], 200);

        Http::fake([
            'api.gomotive.com/v1/vehicles*' => $vehiclesResponseSequence,
            'api.gomotive.com/v1/users*'    => $usersResponseSequence,
        ]);

        $vehicles = Motive::vehicles()->list();
        $users = Motive::users()->list();

        $vehicleCount = iterator_count($vehicles);
        $userCount = iterator_count($users);

        $this->assertEquals(2, $vehicleCount);
        $this->assertEquals(3, $userCount);
    }

    #[Test]
    public function it_handles_early_termination_with_lazy_pagination(): void
    {
        // Simulate a large dataset but only fetch first 50 items
        $perPage = 100;
        $total = 1000;

        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response([
                'vehicles' => array_map(fn ($i) => [
                    'id'         => $i,
                    'company_id' => 100,
                    'number'     => sprintf('TRUCK-%04d', $i),
                    'status'     => 'active',
                ], range(1, $perPage)),
                'pagination' => [
                    'per_page' => $perPage,
                    'page_no'  => 1,
                    'total'    => $total,
                ],
            ], 200),
        ]);

        $vehicles = Motive::vehicles()->list(['per_page' => $perPage]);

        // Only take first 50 items
        $count = 0;
        foreach ($vehicles as $vehicle) {
            $count++;
            if ($count >= 50) {
                break;
            }
        }

        $this->assertEquals(50, $count);

        // Only one HTTP request should have been made
        Http::assertSentCount(1);
    }

    #[Test]
    public function it_handles_empty_result_sets(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response([
                'vehicles'   => [],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 0,
                ],
            ], 200),
        ]);

        $vehicles = Motive::vehicles()->list();

        $count = 0;
        foreach ($vehicles as $vehicle) {
            $count++;
        }

        $this->assertEquals(0, $count);
        Http::assertSentCount(1);
    }

    #[Test]
    public function it_handles_lazy_pagination_with_large_datasets(): void
    {
        // Simulate 10 pages of 100 vehicles each (1000 total)
        $totalPages = 10;
        $perPage = 100;
        $total = $totalPages * $perPage;

        Http::fake(function ($request) use ($perPage, $total) {
            // Parse the page number from the request
            $pageNo = 1;
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
            if (isset($query['page_no'])) {
                $pageNo = (int) $query['page_no'];
            }

            // Generate vehicles for this page
            $vehicles = [];
            $startId = ($pageNo - 1) * $perPage + 1;
            for ($i = 0; $i < $perPage; $i++) {
                $id = $startId + $i;
                $vehicles[] = [
                    'id'         => $id,
                    'company_id' => 100,
                    'number'     => sprintf('TRUCK-%04d', $id),
                    'make'       => 'Freightliner',
                    'model'      => 'Cascadia',
                    'status'     => 'active',
                ];
            }

            return Http::response([
                'vehicles'   => $vehicles,
                'pagination' => [
                    'per_page' => $perPage,
                    'page_no'  => $pageNo,
                    'total'    => $total,
                ],
            ], 200);
        });

        $vehicles = Motive::vehicles()->list(['per_page' => $perPage]);

        // Iterate through all vehicles and count them
        $count = 0;
        $firstVehicle = null;
        $lastVehicle = null;

        foreach ($vehicles as $vehicle) {
            if ($count === 0) {
                $firstVehicle = $vehicle;
            }
            $lastVehicle = $vehicle;
            $count++;

            // Verify each vehicle is properly cast
            $this->assertInstanceOf(Vehicle::class, $vehicle);
        }

        $this->assertEquals($total, $count);
        $this->assertEquals('TRUCK-0001', $firstVehicle->number);
        $this->assertEquals('TRUCK-1000', $lastVehicle->number);

        // Verify that multiple HTTP requests were made (one per page)
        Http::assertSentCount($totalPages);
    }

    #[Test]
    public function it_handles_memory_efficient_iteration_pattern(): void
    {
        $perPage = 100;
        $total = 500;

        Http::fake(function ($request) use ($perPage, $total) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
            $pageNo = isset($query['page_no']) ? (int) $query['page_no'] : 1;

            $vehicles = [];
            $startId = ($pageNo - 1) * $perPage + 1;
            for ($i = 0; $i < $perPage && ($startId + $i) <= $total; $i++) {
                $id = $startId + $i;
                $vehicles[] = [
                    'id'         => $id,
                    'company_id' => 100,
                    'number'     => sprintf('TRUCK-%04d', $id),
                    'status'     => 'active',
                ];
            }

            return Http::response([
                'vehicles'   => $vehicles,
                'pagination' => [
                    'per_page' => $perPage,
                    'page_no'  => $pageNo,
                    'total'    => $total,
                ],
            ], 200);
        });

        $vehicles = Motive::vehicles()->list(['per_page' => $perPage]);

        // Use each() which is memory efficient - doesn't store all items
        $processedIds = [];
        $vehicles->each(function (Vehicle $vehicle) use (&$processedIds) {
            $processedIds[] = $vehicle->id;
        });

        $this->assertCount($total, $processedIds);
        $this->assertEquals(1, $processedIds[0]);
        $this->assertEquals($total, $processedIds[$total - 1]);
    }

    #[Test]
    public function it_handles_pagination_with_filters(): void
    {
        $totalPages = 3;
        $perPage = 50;
        $total = 125; // 3 pages: 50 + 50 + 25

        Http::fake(function ($request) use ($perPage, $total) {
            parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);
            $pageNo = isset($query['page_no']) ? (int) $query['page_no'] : 1;

            // Verify filters are passed
            $this->assertEquals('active', $query['status'] ?? null);

            // Calculate items for this page
            $remaining = $total - (($pageNo - 1) * $perPage);
            $itemCount = min($perPage, $remaining);

            $vehicles = [];
            $startId = ($pageNo - 1) * $perPage + 1;
            for ($i = 0; $i < $itemCount; $i++) {
                $id = $startId + $i;
                $vehicles[] = [
                    'id'         => $id,
                    'company_id' => 100,
                    'number'     => sprintf('TRUCK-%04d', $id),
                    'status'     => 'active',
                ];
            }

            return Http::response([
                'vehicles'   => $vehicles,
                'pagination' => [
                    'per_page' => $perPage,
                    'page_no'  => $pageNo,
                    'total'    => $total,
                ],
            ], 200);
        });

        $vehicles = Motive::vehicles()->list([
            'per_page' => $perPage,
            'status'   => 'active',
        ]);

        $count = iterator_count($vehicles);

        $this->assertEquals($total, $count);
        Http::assertSentCount($totalPages);
    }

    #[Test]
    public function it_handles_single_page_results(): void
    {
        Http::fake([
            'api.gomotive.com/v1/vehicles*' => Http::response([
                'vehicles' => [
                    ['id' => 1, 'company_id' => 100, 'number' => 'TRUCK-001', 'status' => 'active'],
                    ['id' => 2, 'company_id' => 100, 'number' => 'TRUCK-002', 'status' => 'active'],
                    ['id' => 3, 'company_id' => 100, 'number' => 'TRUCK-003', 'status' => 'active'],
                ],
                'pagination' => [
                    'per_page' => 25,
                    'page_no'  => 1,
                    'total'    => 3,
                ],
            ], 200),
        ]);

        $vehicles = Motive::vehicles()->list();

        $count = iterator_count($vehicles);

        $this->assertEquals(3, $count);
        Http::assertSentCount(1);
    }
}
