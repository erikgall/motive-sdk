<?php

namespace Motive\Tests\Unit\Exceptions;

use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\NotFoundException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class NotFoundExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new NotFoundException('Resource not found');

        $this->assertInstanceOf(MotiveException::class, $exception);
        $this->assertEquals('Resource not found', $exception->getMessage());
    }
}
