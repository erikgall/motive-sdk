<?php

namespace Motive\Tests\Unit\Webhooks;

use PHPUnit\Framework\TestCase;
use Motive\Webhooks\WebhookSignature;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookSignatureTest extends TestCase
{
    private const SECRET = 'test-webhook-secret';

    #[Test]
    public function it_generates_consistent_signatures(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';

        $signature1 = WebhookSignature::generate($payload, self::SECRET);
        $signature2 = WebhookSignature::generate($payload, self::SECRET);

        $this->assertSame($signature1, $signature2);
    }

    #[Test]
    public function it_generates_different_signatures_for_different_payloads(): void
    {
        $payload1 = '{"event":"vehicle.updated","data":{}}';
        $payload2 = '{"event":"user.updated","data":{}}';

        $signature1 = WebhookSignature::generate($payload1, self::SECRET);
        $signature2 = WebhookSignature::generate($payload2, self::SECRET);

        $this->assertNotSame($signature1, $signature2);
    }

    #[Test]
    public function it_generates_signature(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';

        $signature = WebhookSignature::generate($payload, self::SECRET);

        $this->assertNotEmpty($signature);
        $this->assertIsString($signature);
    }

    #[Test]
    public function it_rejects_expired_timestamp(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';
        $oldTimestamp = time() - 600; // 10 minutes ago

        $signature = WebhookSignature::generateWithTimestamp($payload, self::SECRET, $oldTimestamp);

        $this->assertFalse(
            WebhookSignature::verifyWithTimestamp(
                $payload,
                $signature,
                self::SECRET,
                $oldTimestamp,
                tolerance: 300 // 5 minutes tolerance
            )
        );
    }

    #[Test]
    public function it_rejects_invalid_signature(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';
        $invalidSignature = 'invalid-signature-12345';

        $this->assertFalse(
            WebhookSignature::verify($payload, $invalidSignature, self::SECRET)
        );
    }

    #[Test]
    public function it_rejects_tampered_payload(): void
    {
        $originalPayload = '{"event":"vehicle.updated","data":{}}';
        $signature = WebhookSignature::generate($originalPayload, self::SECRET);

        $tamperedPayload = '{"event":"vehicle.deleted","data":{}}';

        $this->assertFalse(
            WebhookSignature::verify($tamperedPayload, $signature, self::SECRET)
        );
    }

    #[Test]
    public function it_rejects_wrong_secret(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';
        $signature = WebhookSignature::generate($payload, self::SECRET);

        $this->assertFalse(
            WebhookSignature::verify($payload, $signature, 'wrong-secret')
        );
    }

    #[Test]
    public function it_verifies_signature_with_timestamp(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';
        $timestamp = time();

        $signature = WebhookSignature::generateWithTimestamp($payload, self::SECRET, $timestamp);

        $this->assertTrue(
            WebhookSignature::verifyWithTimestamp(
                $payload,
                $signature,
                self::SECRET,
                $timestamp,
                tolerance: 300
            )
        );
    }

    #[Test]
    public function it_verifies_valid_signature(): void
    {
        $payload = '{"event":"vehicle.updated","data":{}}';
        $signature = WebhookSignature::generate($payload, self::SECRET);

        $this->assertTrue(
            WebhookSignature::verify($payload, $signature, self::SECRET)
        );
    }
}
