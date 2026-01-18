<?php

namespace Motive\Tests\Unit\Testing;

use Motive\Client\Response;
use Motive\Testing\MotiveFake;
use PHPUnit\Framework\TestCase;
use Motive\Testing\FakeResponse;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveFakeTest extends TestCase
{
    #[Test]
    public function it_can_assert_nothing_sent(): void
    {
        $fake = new MotiveFake;

        $this->assertTrue($fake->assertNothingSent());
    }

    #[Test]
    public function it_can_assert_request_was_not_sent(): void
    {
        $fake = new MotiveFake;

        $this->assertTrue($fake->assertNotSent('/v1/vehicles'));
    }

    #[Test]
    public function it_can_assert_request_was_sent(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => []]));

        $fake->get('/v1/vehicles');

        $this->assertTrue($fake->assertSent('/v1/vehicles'));
    }

    #[Test]
    public function it_can_assert_request_was_sent_with_callback(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => []]));

        $fake->get('/v1/vehicles', ['driver_id' => 123]);

        $this->assertTrue($fake->assertSent('/v1/vehicles', function (array $request) {
            return $request['query']['driver_id'] === 123;
        }));
    }

    #[Test]
    public function it_can_assert_sent_count(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => []]));

        $fake->get('/v1/vehicles');
        $fake->get('/v1/vehicles');
        $fake->get('/v1/vehicles');

        $this->assertTrue($fake->assertSentCount(3));
    }

    #[Test]
    public function it_can_be_created(): void
    {
        $fake = new MotiveFake;

        $this->assertInstanceOf(MotiveFake::class, $fake);
    }

    #[Test]
    public function it_can_clear_all_fakes(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => [['id' => 1]]]));

        $response1 = $fake->get('/v1/vehicles');
        $this->assertSame(1, $response1->json('vehicles.0.id'));

        $fake->clearFakes();

        $response2 = $fake->get('/v1/vehicles');
        $this->assertSame([], $response2->json());
    }

    #[Test]
    public function it_can_clear_recorded_requests(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => []]));

        $fake->get('/v1/vehicles');

        $this->assertCount(1, $fake->recorded());

        $fake->clearRecorded();

        $this->assertCount(0, $fake->recorded());
    }

    #[Test]
    public function it_can_fake_delete_request(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/123', FakeResponse::empty(204));

        $response = $fake->delete('/v1/vehicles/123');

        $this->assertSame(204, $response->status());
    }

    #[Test]
    public function it_can_fake_error_response(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/999', FakeResponse::error(404, [
            'error' => 'Vehicle not found',
        ]));

        $response = $fake->get('/v1/vehicles/999');

        $this->assertSame(404, $response->status());
        $this->assertFalse($response->successful());
        $this->assertSame('Vehicle not found', $response->json('error'));
    }

    #[Test]
    public function it_can_fake_get_request(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json([
            'vehicle' => ['id' => 123, 'number' => 'V-001'],
        ]));

        $response = $fake->get('/v1/vehicles');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(123, $response->json('vehicle.id'));
    }

    #[Test]
    public function it_can_fake_patch_request(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/123', FakeResponse::json([
            'vehicle' => ['id' => 123, 'status' => 'inactive'],
        ]));

        $response = $fake->patch('/v1/vehicles/123', ['vehicle' => ['status' => 'inactive']]);

        $this->assertSame('inactive', $response->json('vehicle.status'));
    }

    #[Test]
    public function it_can_fake_post_request(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json([
            'vehicle' => ['id' => 456, 'number' => 'V-002'],
        ], 201));

        $response = $fake->post('/v1/vehicles', ['vehicle' => ['number' => 'V-002']]);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(201, $response->status());
        $this->assertSame(456, $response->json('vehicle.id'));
    }

    #[Test]
    public function it_can_fake_put_request(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/123', FakeResponse::json([
            'vehicle' => ['id' => 123, 'number' => 'V-001-UPDATED'],
        ]));

        $response = $fake->put('/v1/vehicles/123', ['vehicle' => ['number' => 'V-001-UPDATED']]);

        $this->assertSame('V-001-UPDATED', $response->json('vehicle.number'));
    }

    #[Test]
    public function it_can_fake_with_sequence(): void
    {
        $fake = new MotiveFake;

        $fake->fakeSequence('/v1/vehicles', [
            FakeResponse::json(['vehicle' => ['id' => 1]]),
            FakeResponse::json(['vehicle' => ['id' => 2]]),
            FakeResponse::json(['vehicle' => ['id' => 3]]),
        ]);

        $this->assertSame(1, $fake->get('/v1/vehicles')->json('vehicle.id'));
        $this->assertSame(2, $fake->get('/v1/vehicles')->json('vehicle.id'));
        $this->assertSame(3, $fake->get('/v1/vehicles')->json('vehicle.id'));
    }

    #[Test]
    public function it_can_fake_with_wildcard(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/*', FakeResponse::json([
            'vehicle' => ['id' => 999, 'number' => 'WILDCARD'],
        ]));

        $response1 = $fake->get('/v1/vehicles/123');
        $response2 = $fake->get('/v1/vehicles/456');

        $this->assertSame('WILDCARD', $response1->json('vehicle.number'));
        $this->assertSame('WILDCARD', $response2->json('vehicle.number'));
    }

    #[Test]
    public function it_can_use_paginated_response(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::paginated([
            ['id' => 1, 'number' => 'V-001'],
            ['id' => 2, 'number' => 'V-002'],
        ], 10, 25, 'vehicles'));

        $response = $fake->get('/v1/vehicles');

        $this->assertSame(2, count($response->json('vehicles')));
        $this->assertSame(10, $response->json('pagination.total'));
        $this->assertSame(25, $response->json('pagination.per_page'));
    }

    #[Test]
    public function it_matches_exact_path_over_wildcard(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles/*', FakeResponse::json(['match' => 'wildcard']));
        $fake->fake('/v1/vehicles/123', FakeResponse::json(['match' => 'exact']));

        $this->assertSame('exact', $fake->get('/v1/vehicles/123')->json('match'));
        $this->assertSame('wildcard', $fake->get('/v1/vehicles/456')->json('match'));
    }

    #[Test]
    public function it_records_request_history(): void
    {
        $fake = new MotiveFake;

        $fake->fake('/v1/vehicles', FakeResponse::json(['vehicles' => []]));
        $fake->fake('/v1/users', FakeResponse::json(['users' => []]));

        $fake->get('/v1/vehicles', ['page' => 1]);
        $fake->post('/v1/users', ['user' => ['name' => 'John']]);

        $history = $fake->recorded();

        $this->assertCount(2, $history);
        $this->assertSame('GET', $history[0]['method']);
        $this->assertSame('/v1/vehicles', $history[0]['path']);
        $this->assertSame(['page' => 1], $history[0]['query']);
        $this->assertSame('POST', $history[1]['method']);
        $this->assertSame('/v1/users', $history[1]['path']);
        $this->assertSame(['user' => ['name' => 'John']], $history[1]['data']);
    }

    #[Test]
    public function it_returns_empty_response_for_unfaked_url(): void
    {
        $fake = new MotiveFake;

        $response = $fake->get('/v1/unfaked-endpoint');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(200, $response->status());
        $this->assertSame([], $response->json());
    }
}
