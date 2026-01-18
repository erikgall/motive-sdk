<?php

namespace Motive\Tests;

use Motive\Testing\MotiveFake;
use Motive\Testing\FakeResponse;
use Motive\MotiveServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

/**
 * Base test case for the Motive SDK.
 *
 * Provides helper methods and common setup for all tests.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * The MotiveFake instance for testing.
     */
    protected ?MotiveFake $motiveFake = null;

    /**
     * Assert no requests were sent.
     */
    protected function assertNoRequestsSent(): void
    {
        $this->assertRequestCount(0);
    }

    /**
     * Assert that a specific number of requests were sent.
     */
    protected function assertRequestCount(int $count): void
    {
        $this->assertTrue(
            $this->fake()->assertSentCount($count),
            "Expected {$count} requests, but got {$this->fake()->history()->count()}."
        );
    }

    /**
     * Assert that a request was sent to a path.
     */
    protected function assertRequestSent(string $path, ?callable $callback = null): void
    {
        $this->assertTrue(
            $this->fake()->assertSent($path, $callback),
            "Expected request to [{$path}] was not sent."
        );
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('motive.default', 'default');
        $app['config']->set('motive.connections.default', [
            'auth_driver' => 'api_key',
            'api_key'     => 'test-api-key',
            'base_url'    => 'https://api.gomotive.com',
            'timeout'     => 30,
            'retry'       => [
                'times' => 3,
                'sleep' => 100,
            ],
        ]);
    }

    /**
     * Create a MotiveFake instance for testing.
     */
    protected function fake(): MotiveFake
    {
        if ($this->motiveFake === null) {
            $this->motiveFake = new MotiveFake;
        }

        return $this->motiveFake;
    }

    /**
     * Create a fake empty response.
     */
    protected function fakeEmpty(int $status = 200): FakeResponse
    {
        return FakeResponse::empty($status);
    }

    /**
     * Create a fake error response.
     *
     * @param  array<string, mixed>  $body
     */
    protected function fakeError(int $status, array $body = []): FakeResponse
    {
        return FakeResponse::error($status, $body);
    }

    /**
     * Create a fake JSON response.
     *
     * @param  array<string, mixed>  $data
     */
    protected function fakeJson(array $data, int $status = 200): FakeResponse
    {
        return FakeResponse::json($data, $status);
    }

    /**
     * Create a fake paginated response.
     *
     * @param  array<int, array<string, mixed>>  $items
     */
    protected function fakePaginated(
        array $items,
        int $total,
        int $perPage,
        string $key = 'data',
        int $currentPage = 1
    ): FakeResponse {
        return FakeResponse::paginated($items, $total, $perPage, $key, $currentPage);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            MotiveServiceProvider::class,
        ];
    }

    /**
     * Create test data array with defaults.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function makeTestData(array $attributes = []): array
    {
        return array_merge([
            'id'         => rand(1, 10000),
            'created_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ], $attributes);
    }
}
