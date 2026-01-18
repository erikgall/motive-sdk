<?php

namespace Motive\Testing;

use Illuminate\Support\Arr;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * Fake HTTP response for testing.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FakeHttpResponse extends HttpResponse
{
    /**
     * @var array<string, mixed>
     */
    protected array $fakeData;

    /**
     * @var array<string, string>
     */
    protected array $fakeHeaders;

    protected int $fakeStatus;

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, string>  $headers
     */
    public function __construct(array $data, int $status = 200, array $headers = [])
    {
        $this->fakeData = $data;
        $this->fakeStatus = $status;
        $this->fakeHeaders = $headers;

        $body = json_encode($data) ?: '{}';
        $psr7Response = new Psr7Response($status, array_merge(['Content-Type' => 'application/json'], $headers), $body);

        parent::__construct($psr7Response);
    }

    /**
     * Get the JSON decoded body of the response as an array or scalar value.
     *
     * @param  array<string, mixed>|string|null  $key
     * @param  mixed|null  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    public function json($key = null, $default = null): mixed
    {
        if ($key === null) {
            return $this->fakeData;
        }

        return Arr::get($this->fakeData, $key, $default);
    }

    /**
     * Get the status code of the response.
     */
    public function status(): int
    {
        return $this->fakeStatus;
    }

    /**
     * Determine if the response was successful.
     */
    public function successful(): bool
    {
        return $this->fakeStatus >= 200 && $this->fakeStatus < 300;
    }
}
