<?php

namespace Motive\Resources\Companies;

use Motive\Data\Company;
use Motive\Resources\Resource;
use Motive\Resources\Concerns\HasCrudOperations;

/**
 * Resource for managing companies.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CompaniesResource extends Resource
{
    use HasCrudOperations;

    /**
     * Get the currently authenticated company.
     */
    public function current(): Company
    {
        $response = $this->client->get($this->fullPath('current'));
        $data = $response->json($this->resourceKey());

        return Company::from($data);
    }

    protected function basePath(): string
    {
        return 'companies';
    }

    /**
     * @return class-string<Company>
     */
    protected function dtoClass(): string
    {
        return Company::class;
    }

    protected function resourceKey(): string
    {
        return 'company';
    }
}
