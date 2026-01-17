<?php

namespace Motive\Exceptions;

use Exception;
use Throwable;
use Motive\Client\Response;

/**
 * Base exception for all Motive SDK exceptions.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveException extends Exception
{
    public function __construct(
        string $message,
        protected ?Response $response = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the response that caused this exception.
     */
    public function getResponse(): ?Response
    {
        return $this->response;
    }

    /**
     * Get the response body as an array.
     *
     * @return array<string, mixed>|null
     */
    public function getResponseBody(): ?array
    {
        return $this->response?->json();
    }
}
