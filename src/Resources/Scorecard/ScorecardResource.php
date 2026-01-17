<?php

namespace Motive\Resources\Scorecard;

use Motive\Data\Scorecard;
use Motive\Resources\Resource;

/**
 * Resource for retrieving scorecards.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ScorecardResource extends Resource
{
    /**
     * Get scorecard for a specific driver.
     *
     * @param  array<string, mixed>  $params
     */
    public function forDriver(int|string $driverId, array $params = []): Scorecard
    {
        $response = $this->client->get($this->fullPath("drivers/{$driverId}"), $params);

        return Scorecard::from($response->json($this->resourceKey()));
    }

    /**
     * Get scorecard for the entire fleet.
     *
     * @param  array<string, mixed>  $params
     */
    public function forFleet(array $params = []): Scorecard
    {
        $response = $this->client->get($this->fullPath('fleet'), $params);

        return Scorecard::from($response->json($this->resourceKey()));
    }

    protected function basePath(): string
    {
        return 'scorecards';
    }

    /**
     * @return class-string<Scorecard>
     */
    protected function dtoClass(): string
    {
        return Scorecard::class;
    }

    protected function resourceKey(): string
    {
        return 'scorecard';
    }
}
