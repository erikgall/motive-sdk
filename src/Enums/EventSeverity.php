<?php

namespace Motive\Enums;

/**
 * Event severity levels for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum EventSeverity: string
{
    case Critical = 'critical';
    case High = 'high';
    case Low = 'low';
    case Medium = 'medium';
}
