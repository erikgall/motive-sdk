<?php

namespace Motive\Tests\Unit\Exceptions;

use Motive\Client\Response;
use PHPUnit\Framework\TestCase;
use Motive\Exceptions\MotiveException;
use PHPUnit\Framework\Attributes\Test;
use Motive\Exceptions\ValidationException;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class ValidationExceptionTest extends TestCase
{
    #[Test]
    public function it_extends_motive_exception(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertInstanceOf(MotiveException::class, $exception);
    }

    #[Test]
    public function it_extracts_validation_errors_from_response(): void
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->with('errors')->willReturn([
            'number' => ['The number field is required.'],
            'make'   => ['The make field must be a string.'],
        ]);

        $exception = new ValidationException('Validation failed', $response);

        $this->assertEquals([
            'number' => ['The number field is required.'],
            'make'   => ['The make field must be a string.'],
        ], $exception->errors());
    }

    #[Test]
    public function it_returns_empty_array_when_no_errors(): void
    {
        $exception = new ValidationException('Validation failed');

        $this->assertEquals([], $exception->errors());
    }
}
