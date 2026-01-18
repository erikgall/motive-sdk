<?php

namespace Motive\Testing\Factories;

use Motive\Data\DataTransferObject;

/**
 * Base factory class for creating test data.
 *
 * @template TModel of DataTransferObject
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
abstract class Factory
{
    protected int $count = 1;

    protected static int $sequence = 1;

    protected int $sequenceIndex = 0;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $sequenceValues = [];

    /**
     * @var array<string, mixed>
     */
    protected array $states = [];

    /**
     * Set the number of instances to create.
     */
    public function count(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get the default attributes.
     *
     * @return array<string, mixed>
     */
    abstract public function definition(): array;

    /**
     * Get the DTO class name.
     *
     * @return class-string<TModel>
     */
    abstract public function dtoClass(): string;

    /**
     * Create the DTO instance(s).
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel|array<int, TModel>
     */
    public function make(array $attributes = []): DataTransferObject|array
    {
        if ($this->count === 1) {
            return $this->makeOne($attributes);
        }

        $instances = [];

        for ($i = 0; $i < $this->count; $i++) {
            $instances[] = $this->makeOne($attributes);
        }

        return $instances;
    }

    /**
     * Get the raw array data.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<int, array<string, mixed>>|array<string, mixed>
     */
    public function raw(array $attributes = []): array
    {
        if ($this->count === 1) {
            return $this->rawOne($attributes);
        }

        $data = [];

        for ($i = 0; $i < $this->count; $i++) {
            $data[] = $this->rawOne($attributes);
        }

        return $data;
    }

    /**
     * Define a sequence of attribute values.
     *
     * @param  array<string, mixed>  ...$sequence
     */
    public function sequence(array ...$sequence): static
    {
        $this->sequenceValues = $sequence;
        $this->sequenceIndex = 0;

        return $this;
    }

    /**
     * Apply state to the factory.
     *
     * @param  array<string, mixed>  $state
     */
    public function state(array $state): static
    {
        $this->states = array_merge($this->states, $state);

        return $this;
    }

    /**
     * Create a new factory instance.
     */
    public static function new(): static
    {
        return new static;
    }

    /**
     * Generate a unique ID.
     */
    protected function generateId(): int
    {
        return static::$sequence++;
    }

    /**
     * Get the next sequence value.
     *
     * @return array<string, mixed>
     */
    protected function getSequenceValue(): array
    {
        if (empty($this->sequenceValues)) {
            return [];
        }

        $index = $this->sequenceIndex % count($this->sequenceValues);
        $this->sequenceIndex++;

        return $this->sequenceValues[$index];
    }

    /**
     * Create a single DTO instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    protected function makeOne(array $attributes): DataTransferObject
    {
        $data = $this->rawOne($attributes);
        $class = $this->dtoClass();

        return $class::from($data);
    }

    /**
     * Get raw array data for a single instance.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function rawOne(array $attributes): array
    {
        $definition = $this->definition();
        $sequenceValues = $this->getSequenceValue();

        return array_merge($definition, $this->states, $sequenceValues, $attributes);
    }
}
