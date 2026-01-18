<?php

namespace Motive\Testing\Factories;

use Motive\Data\Document;

/**
 * Factory for creating Document test data.
 *
 * @extends Factory<Document>
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentFactory extends Factory
{
    /**
     * @var array<int, string>
     */
    protected static array $types = ['bill_of_lading', 'proof_of_delivery', 'fuel_receipt', 'other'];

    /**
     * Set as approved.
     */
    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    /**
     * Set as bill of lading type.
     */
    public function billOfLading(): static
    {
        return $this->state(['document_type' => 'bill_of_lading']);
    }

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = $this->generateId();

        return [
            'id'            => $id,
            'company_id'    => 1,
            'driver_id'     => rand(1, 100),
            'document_type' => static::$types[array_rand(static::$types)],
            'status'        => 'pending',
            'description'   => 'Document #'.$id,
            'images'        => [],
            'created_at'    => date('Y-m-d\TH:i:s\Z'),
        ];
    }

    /**
     * @return class-string<Document>
     */
    public function dtoClass(): string
    {
        return Document::class;
    }

    /**
     * Set as proof of delivery type.
     */
    public function proofOfDelivery(): static
    {
        return $this->state(['document_type' => 'proof_of_delivery']);
    }

    /**
     * Set as rejected.
     */
    public function rejected(): static
    {
        return $this->state(['status' => 'rejected']);
    }

    /**
     * Set the driver.
     */
    public function withDriver(int $driverId): static
    {
        return $this->state(['driver_id' => $driverId]);
    }
}
