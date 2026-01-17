<?php

namespace Motive\Enums;

/**
 * Webhook status values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum WebhookStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Suspended = 'suspended';
}
