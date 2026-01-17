<?php

namespace Motive\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\WebhookVerificationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookVerificationExceptionTest extends TestCase
{
    #[Test]
    public function it_creates_for_expired_timestamp(): void
    {
        $exception = WebhookVerificationException::expiredTimestamp();

        $this->assertStringContainsString('expired', $exception->getMessage());
    }

    #[Test]
    public function it_creates_for_invalid_signature(): void
    {
        $exception = WebhookVerificationException::invalidSignature();

        $this->assertStringContainsString('Invalid webhook signature', $exception->getMessage());
    }

    #[Test]
    public function it_creates_for_missing_signature(): void
    {
        $exception = WebhookVerificationException::missingSignature();

        $this->assertStringContainsString('Missing', $exception->getMessage());
    }

    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new WebhookVerificationException('Signature mismatch');

        $this->assertInstanceOf(MotiveException::class, $exception);
    }
}
