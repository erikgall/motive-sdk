<?php

namespace Motive\Resources\Users;

use Motive\Data\User;
use Motive\Resources\Resource;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing users.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class UsersResource extends Resource
{
    use HasCrudOperations;

    /**
     * Deactivate a user.
     */
    public function deactivate(int|string $id): bool
    {
        $response = $this->client->post($this->fullPath("{$id}/deactivate"));

        return $response->successful();
    }

    /**
     * Reactivate a user.
     */
    public function reactivate(int|string $id): bool
    {
        $response = $this->client->post($this->fullPath("{$id}/reactivate"));

        return $response->successful();
    }

    protected function basePath(): string
    {
        return 'users';
    }

    /**
     * @return class-string<User>
     */
    protected function dtoClass(): string
    {
        return User::class;
    }

    protected function resourceKey(): string
    {
        return 'user';
    }
}
