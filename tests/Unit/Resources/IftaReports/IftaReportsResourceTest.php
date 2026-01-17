<?php

namespace Motive\Tests\Unit\Resources\IftaReports;

use Motive\Client\Response;
use Motive\Data\IftaReport;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Http\Client\Response as HttpResponse;
use Motive\Resources\IftaReports\IftaReportsResource;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class IftaReportsResourceTest extends TestCase
{
    private MotiveClient $client;

    private IftaReportsResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new IftaReportsResource($this->client);
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $this->assertSame('/v1/ifta_reports', $this->resource->fullPath());
        $this->assertSame('/v1/ifta_reports/123', $this->resource->fullPath('123'));
    }

    #[Test]
    public function it_finds_ifta_report_by_id(): void
    {
        $reportData = [
            'id'          => 123,
            'company_id'  => 456,
            'quarter'     => 1,
            'year'        => 2024,
            'total_miles' => 25000.5,
            'mpg'         => 5.56,
        ];

        $response = $this->createMockResponse(['ifta_report' => $reportData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/ifta_reports/123')
            ->willReturn($response);

        $report = $this->resource->find(123);

        $this->assertInstanceOf(IftaReport::class, $report);
        $this->assertSame(123, $report->id);
        $this->assertSame(25000.5, $report->totalMiles);
    }

    #[Test]
    public function it_generates_ifta_report(): void
    {
        $reportData = [
            'id'         => 125,
            'company_id' => 456,
            'quarter'    => 1,
            'year'       => 2024,
            'status'     => 'processing',
        ];

        $response = $this->createMockResponse(['ifta_report' => $reportData], 201);

        $this->client->expects($this->once())
            ->method('post')
            ->with('/v1/ifta_reports', ['ifta_report' => ['quarter' => 1, 'year' => 2024]])
            ->willReturn($response);

        $report = $this->resource->generate([
            'quarter' => 1,
            'year'    => 2024,
        ]);

        $this->assertInstanceOf(IftaReport::class, $report);
        $this->assertSame(125, $report->id);
        $this->assertSame('processing', $report->status);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('ifta_reports', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('ifta_report', $this->resource->getResourceKey());
    }

    #[Test]
    public function it_lists_ifta_reports(): void
    {
        $reportsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'quarter'    => 1,
                'year'       => 2024,
            ],
            [
                'id'         => 124,
                'company_id' => 456,
                'quarter'    => 2,
                'year'       => 2024,
            ],
        ];

        $response = $this->createMockResponse([
            'ifta_reports' => $reportsData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $this->client->method('get')->willReturn($response);

        $reports = $this->resource->list();

        $this->assertInstanceOf(LazyCollection::class, $reports);

        $reportsArray = $reports->all();
        $this->assertCount(2, $reportsArray);
        $this->assertInstanceOf(IftaReport::class, $reportsArray[0]);
        $this->assertSame(123, $reportsArray[0]->id);
    }

    #[Test]
    public function it_lists_ifta_reports_by_year(): void
    {
        $reportsData = [
            [
                'id'         => 123,
                'company_id' => 456,
                'quarter'    => 1,
                'year'       => 2024,
            ],
        ];

        $response = $this->createMockResponse([
            'ifta_reports' => $reportsData,
            'pagination'   => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/ifta_reports', ['year' => 2024, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $reports = $this->resource->forYear(2024);

        $this->assertInstanceOf(LazyCollection::class, $reports);
        $reportsArray = $reports->all();
        $this->assertCount(1, $reportsArray);
        $this->assertSame(2024, $reportsArray[0]->year);
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
