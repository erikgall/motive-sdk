<?php

namespace Motive\Webhooks;

/**
 * Webhook signature verification and generation.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookSignature
{
    private const ALGORITHM = 'sha256';

    /**
     * Generate a signature for the given payload.
     */
    public static function generate(string $payload, string $secret): string
    {
        return hash_hmac(self::ALGORITHM, $payload, $secret);
    }

    /**
     * Generate a signature with timestamp for the given payload.
     */
    public static function generateWithTimestamp(string $payload, string $secret, int $timestamp): string
    {
        $signedPayload = "{$timestamp}.{$payload}";

        return hash_hmac(self::ALGORITHM, $signedPayload, $secret);
    }

    /**
     * Verify a signature against the payload.
     */
    public static function verify(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = self::generate($payload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify a signature with timestamp validation.
     */
    public static function verifyWithTimestamp(
        string $payload,
        string $signature,
        string $secret,
        int $timestamp,
        int $tolerance = 300
    ): bool {
        // Check if timestamp is within tolerance
        if (abs(time() - $timestamp) > $tolerance) {
            return false;
        }

        $expectedSignature = self::generateWithTimestamp($payload, $secret, $timestamp);

        return hash_equals($expectedSignature, $signature);
    }
}
