<?php

namespace Motive\Resources\HoursOfService;

use Motive\Data\HosLog;
use Motive\Resources\Resource;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing Hours of Service logs.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class HosLogsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Certify a driver's logs for a specific date.
     */
    public function certify(int|string $driverId, string $date): bool
    {
        $response = $this->client->post($this->fullPath('certify'), [
            'driver_id' => $driverId,
            'date'      => $date,
        ]);

        return $response->successful();
    }

    protected function basePath(): string
    {
        return 'hos_logs';
    }

    /**
     * @return class-string<HosLog>
     */
    protected function dtoClass(): string
    {
        return HosLog::class;
    }

    protected function resourceKey(): string
    {
        return 'hos_log';
    }
}
