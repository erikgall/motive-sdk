<?php

namespace Motive\Tests\Unit\Contracts;

use PHPUnit\Framework\TestCase;
use Motive\Contracts\TokenStore;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class TokenStoreContractTest extends TestCase
{
    #[Test]
    public function it_defines_token_store_interface(): void
    {
        $this->assertTrue(interface_exists(TokenStore::class));

        $methods = get_class_methods(TokenStore::class);
        $this->assertContains('getAccessToken', $methods);
        $this->assertContains('getRefreshToken', $methods);
        $this->assertContains('getExpiresAt', $methods);
        $this->assertContains('store', $methods);
        $this->assertContains('clear', $methods);
    }
}
