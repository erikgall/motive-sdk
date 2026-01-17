<?php

namespace Motive\Tests\Unit\Data;

use Motive\Data\User;
use Motive\Data\Driver;
use Motive\Enums\UserRole;
use Carbon\CarbonImmutable;
use Motive\Enums\UserStatus;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class UserTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $user = User::from([
            'id'         => 123,
            'email'      => 'user@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'role'       => 'admin',
            'status'     => 'active',
        ]);

        $array = $user->toArray();

        $this->assertSame(123, $array['id']);
        $this->assertSame('user@example.com', $array['email']);
        $this->assertSame('John', $array['first_name']);
        $this->assertSame('Doe', $array['last_name']);
        $this->assertSame('admin', $array['role']);
        $this->assertSame('active', $array['status']);
    }

    #[Test]
    public function it_creates_from_array(): void
    {
        $user = User::from([
            'id'         => 123,
            'company_id' => 456,
            'email'      => 'user@example.com',
            'first_name' => 'John',
            'last_name'  => 'Doe',
            'phone'      => '+1-555-123-4567',
            'role'       => 'admin',
            'status'     => 'active',
            'created_at' => '2024-01-15T10:30:00Z',
            'updated_at' => '2024-01-15T12:00:00Z',
        ]);

        $this->assertSame(123, $user->id);
        $this->assertSame(456, $user->companyId);
        $this->assertSame('user@example.com', $user->email);
        $this->assertSame('John', $user->firstName);
        $this->assertSame('Doe', $user->lastName);
        $this->assertSame('+1-555-123-4567', $user->phone);
        $this->assertSame(UserRole::Admin, $user->role);
        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertInstanceOf(CarbonImmutable::class, $user->createdAt);
        $this->assertInstanceOf(CarbonImmutable::class, $user->updatedAt);
    }

    #[Test]
    public function it_handles_external_id(): void
    {
        $user = User::from([
            'id'          => 123,
            'email'       => 'user@example.com',
            'external_id' => 'EXT-USER-001',
        ]);

        $this->assertSame('EXT-USER-001', $user->externalId);
    }

    #[Test]
    public function it_handles_nested_driver(): void
    {
        $user = User::from([
            'id'     => 123,
            'email'  => 'driver@example.com',
            'role'   => 'driver',
            'driver' => [
                'id'             => 789,
                'first_name'     => 'John',
                'last_name'      => 'Doe',
                'license_number' => 'D1234567',
            ],
        ]);

        $this->assertInstanceOf(Driver::class, $user->driver);
        $this->assertSame(789, $user->driver->id);
        $this->assertSame('John', $user->driver->firstName);
        $this->assertSame('D1234567', $user->driver->licenseNumber);
    }

    #[Test]
    public function it_handles_optional_fields(): void
    {
        $user = User::from([
            'id'    => 123,
            'email' => 'user@example.com',
        ]);

        $this->assertSame(123, $user->id);
        $this->assertSame('user@example.com', $user->email);
        $this->assertNull($user->companyId);
        $this->assertNull($user->firstName);
        $this->assertNull($user->lastName);
        $this->assertNull($user->phone);
        $this->assertNull($user->role);
        $this->assertNull($user->status);
        $this->assertNull($user->driver);
    }
}
