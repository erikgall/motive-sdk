<?php

namespace Motive\Enums;

/**
 * Driver Vehicle Inspection Report (DVIR) type options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum InspectionType: string
{
    case Dot = 'dot';
    case PostTrip = 'post_trip';
    case PreTrip = 'pre_trip';
}
