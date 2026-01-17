<?php

namespace Motive\Tests\Unit\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPUnit\Framework\TestCase;
use Motive\Webhooks\WebhookSignature;
use PHPUnit\Framework\Attributes\Test;
use Motive\Http\Middleware\VerifyWebhookSignature;
use Motive\Exceptions\WebhookVerificationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class VerifyWebhookSignatureTest extends TestCase
{
    private const SECRET = 'test-webhook-secret';

    #[Test]
    public function it_allows_valid_signature(): void
    {
        $middleware = new VerifyWebhookSignature(self::SECRET);
        $payload = '{"event":"vehicle.updated","data":{}}';
        $signature = WebhookSignature::generate($payload, self::SECRET);

        $request = $this->createRequest($payload, $signature);

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame('OK', $response->getContent());
    }

    #[Test]
    public function it_rejects_expired_timestamp(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('expired');

        $middleware = new VerifyWebhookSignature(self::SECRET, tolerance: 300);
        $payload = '{"event":"vehicle.updated","data":{}}';
        $oldTimestamp = time() - 600; // 10 minutes ago
        $signature = WebhookSignature::generateWithTimestamp($payload, self::SECRET, $oldTimestamp);

        $request = $this->createRequestWithTimestamp($payload, $signature, $oldTimestamp);

        $middleware->handle($request, fn () => new Response('OK'));
    }

    #[Test]
    public function it_rejects_invalid_signature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('Invalid webhook signature');

        $middleware = new VerifyWebhookSignature(self::SECRET);
        $payload = '{"event":"vehicle.updated","data":{}}';

        $request = $this->createRequest($payload, 'invalid-signature');

        $middleware->handle($request, fn () => new Response('OK'));
    }

    #[Test]
    public function it_rejects_missing_signature(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('Missing');

        $middleware = new VerifyWebhookSignature(self::SECRET);
        $payload = '{"event":"vehicle.updated","data":{}}';

        $request = $this->createRequest($payload, null);

        $middleware->handle($request, fn () => new Response('OK'));
    }

    #[Test]
    public function it_rejects_tampered_payload(): void
    {
        $this->expectException(WebhookVerificationException::class);

        $middleware = new VerifyWebhookSignature(self::SECRET);
        $originalPayload = '{"event":"vehicle.updated","data":{}}';
        $signature = WebhookSignature::generate($originalPayload, self::SECRET);

        $tamperedPayload = '{"event":"vehicle.deleted","data":{}}';
        $request = $this->createRequest($tamperedPayload, $signature);

        $middleware->handle($request, fn () => new Response('OK'));
    }

    #[Test]
    public function it_validates_timestamp_when_header_present(): void
    {
        $middleware = new VerifyWebhookSignature(self::SECRET, tolerance: 300);
        $payload = '{"event":"vehicle.updated","data":{}}';
        $timestamp = time();
        $signature = WebhookSignature::generateWithTimestamp($payload, self::SECRET, $timestamp);

        $request = $this->createRequestWithTimestamp($payload, $signature, $timestamp);

        $response = $middleware->handle($request, fn () => new Response('OK'));

        $this->assertSame('OK', $response->getContent());
    }

    /**
     * Create a mock request with signature header.
     */
    private function createRequest(string $payload, ?string $signature): Request
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];

        if ($signature !== null) {
            $headers['HTTP_X_MOTIVE_SIGNATURE'] = $signature;
        }

        return Request::create(
            '/webhooks/motive',
            'POST',
            [],
            [],
            [],
            $headers,
            $payload
        );
    }

    /**
     * Create a mock request with signature and timestamp headers.
     */
    private function createRequestWithTimestamp(string $payload, string $signature, int $timestamp): Request
    {
        return Request::create(
            '/webhooks/motive',
            'POST',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'            => 'application/json',
                'HTTP_X_MOTIVE_SIGNATURE' => $signature,
                'HTTP_X_MOTIVE_TIMESTAMP' => (string) $timestamp,
            ],
            $payload
        );
    }
}
