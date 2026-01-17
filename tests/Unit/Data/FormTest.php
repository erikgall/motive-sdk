<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Form;
use Motive\Data\FormField;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormTest extends TestCase
{
    #[Test]
    public function it_casts_fields_array(): void
    {
        $form = Form::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Test Form',
            'fields'     => [
                ['id' => 1, 'name' => 'field1', 'label' => 'Field 1', 'field_type' => 'text'],
                ['id' => 2, 'name' => 'field2', 'label' => 'Field 2', 'field_type' => 'signature'],
            ],
        ]);

        $this->assertCount(2, $form->fields);
        $this->assertInstanceOf(FormField::class, $form->fields[0]);
        $this->assertSame('field1', $form->fields[0]->name);
    }

    #[Test]
    public function it_converts_to_array(): void
    {
        $form = Form::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Test Form',
            'active'     => true,
        ]);

        $array = $form->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame(456, $array['company_id']);
        $this->assertSame('Test Form', $array['name']);
        $this->assertTrue($array['active']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $form = Form::from([
            'id'          => 123,
            'company_id'  => 456,
            'name'        => 'Delivery Confirmation',
            'description' => 'Form for confirming deliveries',
            'active'      => true,
            'created_at'  => '2024-01-15T10:00:00Z',
            'updated_at'  => '2024-01-15T10:00:00Z',
        ]);

        $this->assertSame(123, $form->id);
        $this->assertSame(456, $form->companyId);
        $this->assertSame('Delivery Confirmation', $form->name);
        $this->assertSame('Form for confirming deliveries', $form->description);
        $this->assertTrue($form->active);
        $this->assertInstanceOf(CarbonImmutable::class, $form->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $form->updatedAt);
    }

    #[Test]
    public function it_handles_nullable_fields(): void
    {
        $form = Form::from([
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'Simple Form',
        ]);

        $this->assertNull($form->description);
        $this->assertTrue($form->active);
        $this->assertEmpty($form->fields);
        $this->assertNull($form->createdAt);
        $this->assertNull($form->updatedAt);
    }
}
