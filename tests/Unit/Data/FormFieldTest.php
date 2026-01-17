<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\FormField;
use Motive\Enums\FormFieldType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormFieldTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $field = FormField::from([
            'id'         => 123,
            'name'       => 'notes',
            'label'      => 'Notes',
            'field_type' => 'textarea',
            'required'   => true,
        ]);

        $array = $field->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('notes', $array['name']);
        $this->assertSame('Notes', $array['label']);
        $this->assertSame('textarea', $array['field_type']);
        $this->assertTrue($array['required']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $field = FormField::from([
            'id'          => 123,
            'name'        => 'driver_signature',
            'label'       => 'Driver Signature',
            'field_type'  => 'signature',
            'required'    => true,
            'placeholder' => 'Sign here',
            'position'    => 1,
        ]);

        $this->assertSame(123, $field->id);
        $this->assertSame('driver_signature', $field->name);
        $this->assertSame('Driver Signature', $field->label);
        $this->assertSame(FormFieldType::Signature, $field->fieldType);
        $this->assertTrue($field->required);
        $this->assertSame('Sign here', $field->placeholder);
        $this->assertSame(1, $field->position);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $field = FormField::from([
            'id'         => 123,
            'name'       => 'notes',
            'label'      => 'Notes',
            'field_type' => 'textarea',
        ]);

        $this->assertFalse($field->required);
        $this->assertNull($field->placeholder);
        $this->assertNull($field->position);
        $this->assertEmpty($field->options);
    }

    #[Test]
    public function it_handles_options_array(): void
    {
        $field = FormField::from([
            'id'         => 123,
            'name'       => 'status',
            'label'      => 'Status',
            'field_type' => 'select',
            'options'    => ['Option A', 'Option B', 'Option C'],
        ]);

        $this->assertCount(3, $field->options);
        $this->assertSame('Option A', $field->options[0]);
    }
}
