<?php

namespace Motive\Exceptions;

/**
 * Exception thrown when webhook signature verification fails.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class WebhookVerificationException extends MotiveException
{
    /**
     * Create an exception for an expired timestamp.
     */
    public static function expiredTimestamp(): self
    {
        return new self('Webhook timestamp has expired.');
    }

    /**
     * Create an exception for an invalid signature.
     */
    public static function invalidSignature(): self
    {
        return new self('Invalid webhook signature.');
    }

    /**
     * Create an exception for a missing signature.
     */
    public static function missingSignature(): self
    {
        return new self('Missing webhook signature header.');
    }
}
