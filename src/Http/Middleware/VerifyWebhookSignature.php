<?php

namespace Motive\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Motive\Webhooks\WebhookSignature;
use Symfony\Component\HttpFoundation\Response;
use Motive\Exceptions\WebhookVerificationException;

/**
 * Middleware to verify webhook signature.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class VerifyWebhookSignature
{
    private const SIGNATURE_HEADER = 'X-Motive-Signature';

    private const TIMESTAMP_HEADER = 'X-Motive-Timestamp';

    public function __construct(
        private readonly string $secret,
        private readonly int $tolerance = 300
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $signature = $request->header(self::SIGNATURE_HEADER);
        $timestamp = $request->header(self::TIMESTAMP_HEADER);
        $payload = $request->getContent();

        if ($signature === null) {
            throw WebhookVerificationException::missingSignature();
        }

        $this->verifySignature($payload, $signature, $timestamp);

        return $next($request);
    }

    /**
     * Verify the webhook signature.
     */
    private function verifySignature(string $payload, string $signature, ?string $timestamp): void
    {
        // If timestamp header is present, verify with timestamp validation
        if ($timestamp !== null) {
            $timestampInt = (int) $timestamp;

            if (! WebhookSignature::verifyWithTimestamp($payload, $signature, $this->secret, $timestampInt, $this->tolerance)) {
                // Check if it's a timestamp issue or signature issue
                if (abs(time() - $timestampInt) > $this->tolerance) {
                    throw WebhookVerificationException::expiredTimestamp();
                }

                throw WebhookVerificationException::invalidSignature();
            }

            return;
        }

        // Simple signature verification without timestamp
        if (! WebhookSignature::verify($payload, $signature, $this->secret)) {
            throw WebhookVerificationException::invalidSignature();
        }
    }
}
