<?php

namespace Motive\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use Motive\Exceptions\ServerException;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ServerExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new ServerException('Internal server error');

        $this->assertInstanceOf(MotiveException::class, $exception);
        $this->assertEquals('Internal server error', $exception->getMessage());
    }
}
