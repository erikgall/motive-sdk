<?php

namespace Motive\Data;

use ReflectionClass;
use JsonSerializable;
use ReflectionProperty;
use ReflectionNamedType;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Base class for all data transfer objects.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @implements Arrayable<string, mixed>
 */
abstract class DataTransferObject implements Arrayable, JsonSerializable
{
    /**
     * Serialize to JSON.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $array = [];
        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $this->{$name};

            $snakeName = Str::snake($name);
            $array[$snakeName] = $this->transformValue($value);
        }

        return $array;
    }

    /**
     * Create a new instance from an array of data.
     *
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): static
    {
        // Apply property mappings first
        $data = static::applyPropertyMappings($data);

        // Allow subclasses to preprocess data
        $data = static::preprocessData($data);

        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (! $constructor) {
            return new static;
        }

        $args = [];
        foreach ($constructor->getParameters() as $parameter) {
            $name = $parameter->getName();
            $snakeName = Str::snake($name);

            $value = $data[$name] ?? $data[$snakeName] ?? null;

            if ($value === null && $parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            }

            $type = $parameter->getType();
            if ($type instanceof ReflectionNamedType) {
                $value = static::castValue($value, $type, $name);
            }

            $args[$name] = $value;
        }

        return new static(...$args);
    }

    /**
     * Transform a value for array/JSON output.
     */
    protected function transformValue(mixed $value): mixed
    {
        if ($value instanceof CarbonImmutable) {
            return $value->toIso8601String();
        }

        if ($value instanceof self) {
            return $value->toArray();
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->transformValue($item), $value);
        }

        return $value;
    }

    /**
     * Apply property mappings to data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function applyPropertyMappings(array $data): array
    {
        $mappings = static::propertyMappings();

        foreach ($mappings as $from => $to) {
            if (array_key_exists($from, $data)) {
                $data[$to] = $data[$from];
                unset($data[$from]);
            }
        }

        return $data;
    }

    /**
     * Cast a value to its appropriate type.
     */
    protected static function castValue(mixed $value, ReflectionNamedType $type, string $propertyName = ''): mixed
    {
        if ($value === null) {
            return null;
        }

        $typeName = $type->getName();

        // Handle Carbon types
        if ($typeName === CarbonImmutable::class || $typeName === 'Carbon\CarbonImmutable') {
            if ($value instanceof CarbonImmutable) {
                return $value;
            }

            return CarbonImmutable::parse($value);
        }

        // Handle enums
        if (enum_exists($typeName)) {
            if ($value instanceof $typeName) {
                return $value;
            }

            return $typeName::from($value);
        }

        // Handle nested DTOs
        if (is_subclass_of($typeName, self::class) && is_array($value)) {
            return $typeName::from($value);
        }

        // Handle arrays of nested DTOs
        if ($typeName === 'array' && is_array($value) && $propertyName !== '') {
            $nestedArrays = static::nestedArrays();
            if (isset($nestedArrays[$propertyName])) {
                $dtoClass = $nestedArrays[$propertyName];

                return array_map(fn (array $item) => $dtoClass::from($item), $value);
            }
        }

        return $value;
    }

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * Override this method to define nested array mappings.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [];
    }

    /**
     * Preprocess data before hydration.
     *
     * Override this method to transform data before it's hydrated into the DTO.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected static function preprocessData(array $data): array
    {
        return $data;
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * Override this method to define custom key mappings.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [];
    }
}
