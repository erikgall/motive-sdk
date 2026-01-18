<?php

namespace Motive\Data;

use Motive\Enums\FormFieldType;

/**
 * Form field data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property string $name
 * @property string $label
 * @property FormFieldType $fieldType
 * @property bool $required
 * @property string|null $placeholder
 * @property int|null $position
 * @property array<int, string> $options
 */
class FormField extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'        => 'int',
        'position'  => 'int',
        'required'  => 'bool',
        'fieldType' => FormFieldType::class,
    ];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'required' => false,
        'options'  => [],
    ];
}
