<?php

namespace Motive\Tests\Unit\Resources\Scorecard;

use Motive\Data\Scorecard;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Scorecard\ScorecardResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ScorecardResourceTest extends TestCase
{
    private MotiveClient $client;

    private ScorecardResource $resource;

    protected function setUp(): void
    {
        $this->client = $this->createMock(MotiveClient::class);
        $this->resource = new ScorecardResource($this->client);
    }

    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $this->assertSame('/v1/scorecards', $this->resource->fullPath());
    }

    #[Test]
    public function it_gets_scorecard_for_driver(): void
    {
        $scorecardData = [
            'id'            => 123,
            'company_id'    => 456,
            'driver_id'     => 789,
            'overall_score' => 85.5,
            'safety_score'  => 90.0,
        ];

        $response = $this->createMockResponse(['scorecard' => $scorecardData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/scorecards/drivers/789', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'])
            ->willReturn($response);

        $scorecard = $this->resource->forDriver(789, [
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
        ]);

        $this->assertInstanceOf(Scorecard::class, $scorecard);
        $this->assertSame(85.5, $scorecard->overallScore);
        $this->assertSame(789, $scorecard->driverId);
    }

    #[Test]
    public function it_gets_scorecard_for_fleet(): void
    {
        $scorecardData = [
            'id'            => 124,
            'company_id'    => 456,
            'overall_score' => 82.0,
            'safety_score'  => 85.0,
            'total_miles'   => 150000.5,
        ];

        $response = $this->createMockResponse(['scorecard' => $scorecardData]);

        $this->client->expects($this->once())
            ->method('get')
            ->with('/v1/scorecards/fleet', ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'])
            ->willReturn($response);

        $scorecard = $this->resource->forFleet([
            'start_date' => '2024-01-01',
            'end_date'   => '2024-01-31',
        ]);

        $this->assertInstanceOf(Scorecard::class, $scorecard);
        $this->assertSame(82.0, $scorecard->overallScore);
        $this->assertSame(150000.5, $scorecard->totalMiles);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $this->assertSame('scorecards', $this->resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $this->assertSame('scorecard', $this->resource->getResourceKey());
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
