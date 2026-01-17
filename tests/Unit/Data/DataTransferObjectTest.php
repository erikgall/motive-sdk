<?php

namespace Motive\Tests\Unit\Data;

use JsonSerializable;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\TestCase;
use Motive\Data\DataTransferObject;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Contracts\Support\Arrayable;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DataTransferObjectTest extends TestCase
{
    #[Test]
    public function it_converts_to_array(): void
    {
        $dto = new TestDto(
            id: 123,
            name: 'Test',
            createdAt: CarbonImmutable::parse('2024-01-17T10:00:00Z')
        );

        $array = $dto->toArray();

        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Test', $array['name']);
        $this->assertIsString($array['created_at']);
    }

    #[Test]
    public function it_handles_camel_case_properties(): void
    {
        $dto = TestDto::from([
            'id'         => 1,
            'name'       => 'Test',
            'created_at' => '2024-01-17T10:00:00Z',
        ]);

        $this->assertInstanceOf(CarbonImmutable::class, $dto->createdAt);
    }

    #[Test]
    public function it_handles_null_values(): void
    {
        $dto = TestDto::from([
            'id'         => 1,
            'name'       => 'Test',
            'created_at' => null,
        ]);

        $this->assertNull($dto->createdAt);
    }

    #[Test]
    public function it_hydrates_from_array(): void
    {
        $dto = TestDto::from([
            'id'         => 123,
            'name'       => 'Test',
            'created_at' => '2024-01-17T10:00:00Z',
        ]);

        $this->assertEquals(123, $dto->id);
        $this->assertEquals('Test', $dto->name);
        $this->assertInstanceOf(CarbonImmutable::class, $dto->createdAt);
    }

    #[Test]
    public function it_ignores_unknown_properties(): void
    {
        $dto = TestDto::from([
            'id'            => 123,
            'name'          => 'Test',
            'created_at'    => '2024-01-17T10:00:00Z',
            'unknown_field' => 'should be ignored',
        ]);

        $this->assertEquals(123, $dto->id);
    }

    #[Test]
    public function it_implements_arrayable(): void
    {
        $dto = new TestDto(
            id: 123,
            name: 'Test',
            createdAt: null
        );

        $this->assertInstanceOf(Arrayable::class, $dto);
    }

    #[Test]
    public function it_is_json_serializable(): void
    {
        $dto = new TestDto(
            id: 123,
            name: 'Test',
            createdAt: CarbonImmutable::parse('2024-01-17T10:00:00Z')
        );

        $this->assertInstanceOf(JsonSerializable::class, $dto);

        $json = json_encode($dto);
        $decoded = json_decode($json, true);

        $this->assertEquals(123, $decoded['id']);
        $this->assertEquals('Test', $decoded['name']);
    }
}

class TestDto extends DataTransferObject
{
    public function __construct(
        public int $id,
        public string $name,
        public ?CarbonImmutable $createdAt
    ) {}
}
