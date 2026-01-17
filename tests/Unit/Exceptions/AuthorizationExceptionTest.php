<?php

namespace Motive\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\AuthorizationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class AuthorizationExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new AuthorizationException('Access denied');

        $this->assertInstanceOf(MotiveException::class, $exception);
        $this->assertEquals('Access denied', $exception->getMessage());
    }
}
