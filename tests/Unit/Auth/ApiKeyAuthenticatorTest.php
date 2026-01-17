<?php

namespace Motive\Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use Motive\Client\PendingRequest;
use Motive\Contracts\Authenticator;
use Motive\Auth\ApiKeyAuthenticator;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ApiKeyAuthenticatorTest extends TestCase
{
    #[Test]
    public function it_adds_api_key_header_to_request(): void
    {
        $apiKey = 'test-api-key-123';
        $authenticator = new ApiKeyAuthenticator($apiKey);

        $request = $this->createMock(PendingRequest::class);
        $request->expects($this->once())
            ->method('withHeader')
            ->with('X-Api-Key', $apiKey)
            ->willReturnSelf();

        $result = $authenticator->authenticate($request);

        $this->assertSame($request, $result);
    }

    #[Test]
    public function it_implements_authenticator_contract(): void
    {
        $authenticator = new ApiKeyAuthenticator('test-api-key');

        $this->assertInstanceOf(Authenticator::class, $authenticator);
    }

    #[Test]
    public function it_never_expires(): void
    {
        $authenticator = new ApiKeyAuthenticator('key');

        $this->assertFalse($authenticator->isExpired());
    }

    #[Test]
    public function refresh_does_nothing_for_api_keys(): void
    {
        $authenticator = new ApiKeyAuthenticator('key');

        // Should not throw
        $authenticator->refresh();
        $this->assertTrue(true);
    }
}
