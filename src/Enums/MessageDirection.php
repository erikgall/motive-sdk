<?php

namespace Motive\Enums;

/**
 * Message direction values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum MessageDirection: string
{
    case Inbound = 'inbound';
    case Outbound = 'outbound';
}
