<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\Company;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class CompanyTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $company = Company::from([
            'id'         => 123,
            'name'       => 'Acme Trucking',
            'dot_number' => '123456',
            'timezone'   => 'America/Los_Angeles',
        ]);

        $array = $company->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('Acme Trucking', $array['name']);
        $this->assertSame('123456', $array['dot_number']);
        $this->assertSame('America/Los_Angeles', $array['timezone']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $company = Company::from([
            'id'         => 123,
            'name'       => 'Acme Trucking',
            'dot_number' => '123456',
            'mc_number'  => 'MC654321',
            'address'    => '123 Main St',
            'city'       => 'San Francisco',
            'state'      => 'CA',
            'zip'        => '94105',
            'country'    => 'US',
            'phone'      => '+1-555-123-4567',
            'timezone'   => 'America/Los_Angeles',
            'created_at' => '2024-01-15T10:30:00Z',
            'updated_at' => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $company->id);
        $this->assertSame('Acme Trucking', $company->name);
        $this->assertSame('123456', $company->dotNumber);
        $this->assertSame('MC654321', $company->mcNumber);
        $this->assertSame('123 Main St', $company->address);
        $this->assertSame('San Francisco', $company->city);
        $this->assertSame('CA', $company->state);
        $this->assertSame('94105', $company->zip);
        $this->assertSame('US', $company->country);
        $this->assertSame('+1-555-123-4567', $company->phone);
        $this->assertSame('America/Los_Angeles', $company->timezone);
        $this->assertInstanceOf(CarbonImmutable::class, $company->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $company->updatedAt);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $company = Company::from([
            'id'   => 123,
            'name' => 'Acme Trucking',
        ]);

        $this->assertSame(123, $company->id);
        $this->assertSame('Acme Trucking', $company->name);
        $this->assertNull($company->dotNumber);
        $this->assertNull($company->mcNumber);
        $this->assertNull($company->address);
        $this->assertNull($company->city);
        $this->assertNull($company->state);
        $this->assertNull($company->zip);
        $this->assertNull($company->country);
        $this->assertNull($company->phone);
        $this->assertNull($company->timezone);
    }
}
