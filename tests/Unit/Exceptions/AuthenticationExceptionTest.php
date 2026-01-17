<?php

namespace Motive\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\AuthenticationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AuthenticationExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new AuthenticationException('Invalid credentials');

        $this->assertInstanceOf(MotiveException::class, $exception);
        $this->assertEquals('Invalid credentials', $exception->getMessage());
    }
}
