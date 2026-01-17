<?php

namespace Motive\Resources\Groups;

use Motive\Data\Group;
use Motive\Data\GroupMember;
use Motive\Resources\Resource;
use Illuminate\Support\Collection;
use Motive\Pagination\LazyPaginator;
use Illuminate\Support\LazyCollection;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing groups.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class GroupsResource extends Resource
{
    use HasCrudOperations;

    /**
     * Add a member to a group.
     */
    public function addMember(int|string $groupId, int|string $memberId, string $memberType): bool
    {
        $response = $this->client->post($this->fullPath("{$groupId}/members"), [
            'member_id'   => $memberId,
            'member_type' => $memberType,
        ]);

        return $response->successful();
    }

    /**
     * List all groups.
     *
     * @param  array<string, mixed>  $params
     * @return LazyCollection<int, Group>
     */
    public function list(array $params = []): LazyCollection
    {
        $lazyPaginator = new LazyPaginator(
            client: $this->client,
            path: $this->fullPath(),
            resourceKey: $this->getPluralResourceKey(),
            params: $params
        );

        return $lazyPaginator->cursor()->map(fn (array $item) => Group::from($item));
    }

    /**
     * Get members of a group.
     *
     * @return Collection<int, GroupMember>
     */
    public function members(int|string $groupId): Collection
    {
        $response = $this->client->get($this->fullPath("{$groupId}/members"));
        $data = $response->json('group_members') ?? [];

        return collect(array_map(fn (array $item) => GroupMember::from($item), $data));
    }

    /**
     * Remove a member from a group.
     */
    public function removeMember(int|string $groupId, int|string $memberId): bool
    {
        $response = $this->client->delete($this->fullPath("{$groupId}/members/{$memberId}"));

        return $response->successful();
    }

    protected function basePath(): string
    {
        return 'groups';
    }

    /**
     * @return class-string<Group>
     */
    protected function dtoClass(): string
    {
        return Group::class;
    }

    protected function resourceKey(): string
    {
        return 'group';
    }
}
