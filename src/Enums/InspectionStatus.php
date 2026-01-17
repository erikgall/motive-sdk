<?php

namespace Motive\Enums;

/**
 * Driver Vehicle Inspection Report (DVIR) status options.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum InspectionStatus: string
{
    case Corrected = 'corrected';
    case Failed = 'failed';
    case Passed = 'passed';
    case Satisfactory = 'satisfactory';
}
