<?php

namespace Motive\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use Motive\Enums\DocumentStatus;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentStatusTest extends TestCase
{
    #[Test]
    public function it_can_be_created_from_string(): void
    {
        $status = DocumentStatus::from('pending');

        $this->assertSame(DocumentStatus::Pending, $status);
    }

    #[Test]
    public function it_has_approved_status(): void
    {
        $this->assertSame('approved', DocumentStatus::Approved->value);
    }

    #[Test]
    public function it_has_pending_status(): void
    {
        $this->assertSame('pending', DocumentStatus::Pending->value);
    }

    #[Test]
    public function it_has_rejected_status(): void
    {
        $this->assertSame('rejected', DocumentStatus::Rejected->value);
    }
}
