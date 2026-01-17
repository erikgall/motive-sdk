<?php

namespace Motive\Resources\Webhooks;

use Motive\Data\Webhook;
use Motive\Data\WebhookLog;
use Motive\Resources\Resource;
use Illuminate\Support\Collection;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing webhooks.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhooksResource extends Resource
{
    use HasCrudOperations;

    protected string $apiVersion = '2';

    /**
     * List all webhooks.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Webhook>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Webhook::from($item));
    }

    /**
     * Get webhook delivery logs.
     *
     * @return Collection<int, WebhookLog>
     */
    public function logs(int|string $webhookId): Collection
    {
        $response = $this->client->get($this->fullPath("{$webhookId}/logs"));
        $data = $response->json('webhook_logs') ?? [];

        return collect(array_map(fn (array $item) => WebhookLog::from($item), $data));
    }

    /**
     * Test a webhook by sending a test payload.
     */
    public function test(int|string $webhookId): bool
    {
        $response = $this->client->post($this->fullPath("{$webhookId}/test"));

        return $response->successful();
    }

    protected function basePath(): string
    {
        return 'webhooks';
    }

    /**
     * @return class-string<Webhook>
     */
    protected function dtoClass(): string
    {
        return Webhook::class;
    }

    protected function resourceKey(): string
    {
        return 'webhook';
    }
}
