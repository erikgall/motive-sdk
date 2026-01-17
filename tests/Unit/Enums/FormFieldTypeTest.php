<?php

namespace Motive\Tests\Unit\Enums;

use Motive\Enums\FormFieldType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormFieldTypeTest extends TestCase
{
    #[Test]
    public function it_creates_from_value(): void
    {
        $type = FormFieldType::from('signature');

        $this->assertSame(FormFieldType::Signature, $type);
    }

    #[Test]
    public function it_has_expected_cases(): void
    {
        $this->assertSame('text', FormFieldType::Text->value);
        $this->assertSame('number', FormFieldType::Number->value);
        $this->assertSame('date', FormFieldType::Date->value);
        $this->assertSame('datetime', FormFieldType::DateTime->value);
        $this->assertSame('checkbox', FormFieldType::Checkbox->value);
        $this->assertSame('select', FormFieldType::Select->value);
        $this->assertSame('textarea', FormFieldType::Textarea->value);
        $this->assertSame('signature', FormFieldType::Signature->value);
        $this->assertSame('photo', FormFieldType::Photo->value);
    }
}
