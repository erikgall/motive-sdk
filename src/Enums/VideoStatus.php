<?php

namespace Motive\Enums;

/**
 * Video request status values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum VideoStatus: string
{
    case Completed = 'completed';
    case Failed = 'failed';
    case Pending = 'pending';
    case Processing = 'processing';
}
