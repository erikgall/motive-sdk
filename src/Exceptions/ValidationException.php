<?php

namespace Motive\Exceptions;

/**
 * Exception thrown when validation fails (422).
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class ValidationException extends MotiveException
{
    /**
     * Get the validation errors.
     *
     * @return array<string, array<string>>
     */
    public function errors(): array
    {
        return $this->response?->json('errors') ?? [];
    }
}
