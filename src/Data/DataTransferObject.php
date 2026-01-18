<?php

namespace Motive\Data;

use BackedEnum;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\Support\Fluent;

/**
 * Base class for all data transfer objects.
 *
 * Extends Laravel's Fluent class to provide a fluent interface for
 * working with API response data while supporting type casting for
 * enums, dates, and nested DTOs.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
abstract class DataTransferObject extends Fluent
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [];

    /**
     * Default values for properties.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [];

    /**
     * Property mappings from API response keys to class properties.
     *
     * @var array<string, string>
     */
    protected array $mappings = [];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [];

    /**
     * Create a new data transfer object instance.
     *
     * @param  array<string, mixed>|object  $attributes
     */
    public function __construct($attributes = [])
    {
        $attributes = $this->prepareAttributes($attributes);
        parent::__construct($attributes);
    }

    /**
     * Get an attribute from the fluent instance.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        // Use default from property if available
        $effectiveDefault = $this->defaults[$key] ?? $default;
        $value = parent::get($key, $effectiveDefault);

        return $this->castAttribute($key, $value);
    }

    /**
     * Get the underlying attributes.
     *
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Convert the fluent instance to an array.
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->getAttributes() as $key => $value) {
            $snakeKey = Str::snake($key);
            $array[$snakeKey] = $this->transformValue($this->castAttribute($key, $value));
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
        return new static($data);
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Cast an attribute to its proper type.
     */
    protected function castAttribute(string $key, mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        // Check if it's a nested array of DTOs
        if (isset($this->nestedArrays[$key]) && is_array($value)) {
            $dtoClass = $this->nestedArrays[$key];

            return array_map(fn (array $item) => $dtoClass::from($item), $value);
        }

        // Check for casts
        if (! isset($this->casts[$key])) {
            return $value;
        }

        $castType = $this->casts[$key];

        // Handle Carbon dates
        if ($castType === CarbonImmutable::class || $castType === 'datetime' || $castType === 'date') {
            if ($value instanceof CarbonImmutable) {
                return $value;
            }

            return CarbonImmutable::parse($value);
        }

        // Handle enums
        if (enum_exists($castType)) {
            if ($value instanceof $castType) {
                return $value;
            }

            return $castType::from($value);
        }

        // Handle nested DTOs
        if (is_subclass_of($castType, self::class) && is_array($value)) {
            return $castType::from($value);
        }

        // Handle primitive casts
        return match ($castType) {
            'int', 'integer' => (int) $value,
            'float', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array' => (array) $value,
            default => $value,
        };
    }

    /**
     * Prepare attributes before storing.
     *
     * @param  array<string, mixed>|object  $attributes
     * @return array<string, mixed>
     */
    protected function prepareAttributes($attributes): array
    {
        if (is_object($attributes)) {
            $attributes = get_object_vars($attributes);
        }

        // Apply property mappings
        foreach ($this->mappings as $from => $to) {
            if (array_key_exists($from, $attributes)) {
                $attributes[$to] = $attributes[$from];
                unset($attributes[$from]);
            }
        }

        // Convert snake_case keys to camelCase
        $normalized = [];
        foreach ($attributes as $key => $value) {
            $camelKey = Str::camel($key);
            $normalized[$camelKey] = $value;
        }

        // Allow subclasses to process the data
        return $this->processAttributes($normalized);
    }

    /**
     * Process attributes after normalization.
     *
     * Override this method to customize attribute processing.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function processAttributes(array $attributes): array
    {
        return $attributes;
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

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->transformValue($item), $value);
        }

        return $value;
    }
}
