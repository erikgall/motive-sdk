<?php

namespace Motive\Tests\Unit;

use Motive\Tests\TestCase;
use Motive\Testing\MotiveFake;
use Motive\Testing\FakeResponse;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TestCaseTest extends TestCase
{
    #[Test]
    public function it_configures_motive_settings(): void
    {
        $this->assertEquals('test-api-key', config('motive.connections.default.api_key'));
        $this->assertEquals('https://api.gomotive.com', config('motive.connections.default.base_url'));
    }

    #[Test]
    public function it_creates_fake_empty_response(): void
    {
        $response = $this->fakeEmpty(204);

        $this->assertInstanceOf(FakeResponse::class, $response);
    }

    #[Test]
    public function it_creates_fake_error_response(): void
    {
        $response = $this->fakeError(400, ['error' => 'Bad request']);

        $this->assertInstanceOf(FakeResponse::class, $response);
    }

    #[Test]
    public function it_creates_fake_json_response(): void
    {
        $response = $this->fakeJson(['key' => 'value'], 200);

        $this->assertInstanceOf(FakeResponse::class, $response);
    }

    #[Test]
    public function it_creates_fake_paginated_response(): void
    {
        $response = $this->fakePaginated([['id' => 1]], 1, 25, 'items');

        $this->assertInstanceOf(FakeResponse::class, $response);
    }

    #[Test]
    public function it_creates_motive_fake_instance(): void
    {
        $fake = $this->fake();

        $this->assertInstanceOf(MotiveFake::class, $fake);
    }

    #[Test]
    public function it_creates_test_data_with_defaults(): void
    {
        $data = $this->makeTestData(['name' => 'Test']);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
        $this->assertEquals('Test', $data['name']);
    }

    #[Test]
    public function it_returns_same_fake_instance_on_multiple_calls(): void
    {
        $fake1 = $this->fake();
        $fake2 = $this->fake();

        $this->assertSame($fake1, $fake2);
    }
}
