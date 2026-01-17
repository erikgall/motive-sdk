<?php

namespace Motive\Enums;

/**
 * Form field type values for the Motive API.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
enum FormFieldType: string
{
    case Checkbox = 'checkbox';
    case Date = 'date';
    case DateTime = 'datetime';
    case Number = 'number';
    case Photo = 'photo';
    case Select = 'select';
    case Signature = 'signature';
    case Text = 'text';
    case Textarea = 'textarea';
}
