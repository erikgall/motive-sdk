<?php

namespace Motive\Tests\Unit\Contracts;

use PHPUnit\Framework\TestCase;
use Motive\Contracts\Authenticator;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AuthenticatorContractTest extends TestCase
{
    #[Test]
    public function it_defines_authenticator_interface(): void
    {
        $this->assertTrue(interface_exists(Authenticator::class));

        $methods = get_class_methods(Authenticator::class);
        $this->assertContains('authenticate', $methods);
        $this->assertContains('isExpired', $methods);
        $this->assertContains('refresh', $methods);
    }
}
