<?php

namespace Motive\Data;

use Motive\Enums\FormFieldType;

/**
 * Form field data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormField extends DataTransferObject
{
    /**
     * @param  array<int, string>  $options
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $label,
        public FormFieldType $fieldType,
        public bool $required = false,
        public ?string $placeholder = null,
        public ?int $position = null,
        public array $options = []
    ) {}

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'fieldType' => FormFieldType::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'field_type' => 'fieldType',
        ];
    }
}
