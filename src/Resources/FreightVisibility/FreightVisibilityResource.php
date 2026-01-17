<?php

namespace Motive\Resources\FreightVisibility;

use Motive\Data\Shipment;
use Motive\Data\ShipmentEta;
use Motive\Resources\Resource;
use Motive\Data\ShipmentTracking;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;

/**
 * Resource for freight visibility and shipment tracking.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FreightVisibilityResource extends Resource
{
    /**
     * Get the ETA for a shipment.
     */
    public function eta(int|string $shipmentId): ShipmentEta
    {
        $response = $this->client->get($this->fullPath("{$shipmentId}/eta"));

        return ShipmentEta::from($response->json('shipment_eta'));
    }

    /**
     * List all shipments.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Shipment>
     */
    public function shipments(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Shipment::from($item));
    }

    /**
     * Get tracking information for a shipment.
     */
    public function tracking(int|string $shipmentId): ShipmentTracking
    {
        $response = $this->client->get($this->fullPath("{$shipmentId}/tracking"));

        return ShipmentTracking::from($response->json('shipment_tracking'));
    }

    protected function basePath(): string
    {
        return 'shipments';
    }

    /**
     * @return class-string<Shipment>
     */
    protected function dtoClass(): string
    {
        return Shipment::class;
    }

    protected function resourceKey(): string
    {
        return 'shipment';
    }
}
