<?php

namespace Motive\Tests\Unit\Resources\Companies;

use Motive\Data\Company;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Companies\CompaniesResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CompaniesResourceTest extends TestCase
{
    private MotiveClient $client;

    private CompaniesResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new CompaniesResource($this->client);
    }

    #[Test]
    public function it_finds_company_by_id(): void
    {
        $companyData = [
            'id'         => 456,
            'name'       => 'XYZ Transport',
            'dot_number' => '789012',
        ];

        $response = $this->createMockResponse(['company' => $companyData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/companies/456')
            ->willReturn($response);

        $company = $this->resource->find(456);

        $this->assertInstanceOf(Company::class, $company);
        $this->assertSame(456, $company->id);
        $this->assertSame('XYZ Transport', $company->name);
    }

    #[Test]
    public function it_gets_current_company(): void
    {
        $companyData = [
            'id'         => 123,
            'name'       => 'Acme Trucking',
            'dot_number' => '123456',
            'timezone'   => 'America/Los_Angeles',
        ];

        $response = $this->createMockResponse(['company' => $companyData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/companies/current')
            ->willReturn($response);

        $company = $this->resource->current();

        $this->assertInstanceOf(Company::class, $company);
        $this->assertSame(123, $company->id);
        $this->assertSame('Acme Trucking', $company->name);
        $this->assertSame('123456', $company->dotNumber);
        $this->assertSame('America/Los_Angeles', $company->timezone);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('companies', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('company', $this->resource->getResourceKey());
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createMock(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}
