<?php

namespace Motive\Tests\Unit\Pagination;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Pagination\PaginatedResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class PaginatedResponseTest extends TestCase
{
    #[Test]
    public function it_calculates_last_page(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 100,
            perPage: 25,
            currentPage: 1
        );

        $this->assertEquals(4, $response->lastPage());
    }

    #[Test]
    public function it_calculates_last_page_with_remainder(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 101,
            perPage: 25,
            currentPage: 1
        );

        $this->assertEquals(5, $response->lastPage());
    }

    #[Test]
    public function it_detects_more_pages(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 100,
            perPage: 25,
            currentPage: 2
        );

        $this->assertTrue($response->hasMorePages());

        $response2 = new PaginatedResponse(
            items: new Collection([]),
            total: 100,
            perPage: 25,
            currentPage: 4
        );

        $this->assertFalse($response2->hasMorePages());
    }

    #[Test]
    public function it_holds_paginated_items(): void
    {
        $items = new Collection([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ]);

        $response = new PaginatedResponse(
            items: $items,
            total: 100,
            perPage: 10,
            currentPage: 1
        );

        $this->assertInstanceOf(Collection::class, $response->items());
        $this->assertCount(3, $response->items());
    }

    #[Test]
    public function it_is_countable(): void
    {
        $items = new Collection([
            ['id' => 1],
            ['id' => 2],
        ]);

        $response = new PaginatedResponse(
            items: $items,
            total: 100,
            perPage: 10,
            currentPage: 1
        );

        $this->assertCount(2, $response);
    }

    #[Test]
    public function it_is_iterable(): void
    {
        $items = new Collection([
            ['id' => 1],
            ['id' => 2],
        ]);

        $response = new PaginatedResponse(
            items: $items,
            total: 100,
            perPage: 10,
            currentPage: 1
        );

        $count = 0;
        foreach ($response as $item) {
            $count++;
        }

        $this->assertEquals(2, $count);
    }

    #[Test]
    public function it_returns_current_page(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 100,
            perPage: 25,
            currentPage: 3
        );

        $this->assertEquals(3, $response->currentPage());
    }

    #[Test]
    public function it_returns_item_count(): void
    {
        $items = new Collection([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ]);

        $response = new PaginatedResponse(
            items: $items,
            total: 100,
            perPage: 10,
            currentPage: 1
        );

        $this->assertEquals(3, $response->count());
    }

    #[Test]
    public function it_returns_per_page(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 100,
            perPage: 25,
            currentPage: 1
        );

        $this->assertEquals(25, $response->perPage());
    }

    #[Test]
    public function it_returns_total_count(): void
    {
        $response = new PaginatedResponse(
            items: new Collection([]),
            total: 150,
            perPage: 25,
            currentPage: 1
        );

        $this->assertEquals(150, $response->total());
    }
}
