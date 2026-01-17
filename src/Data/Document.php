<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DocumentType;
use Motive\Enums\DocumentStatus;

/**
 * Document data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Document extends DataTransferObject
{
    /**
     * @param  array<int, DocumentImage>  $images
     */
    public function __construct(
        public int $id,
        public int $companyId,
        public DocumentType $documentType,
        public DocumentStatus $status,
        public ?int $driverId = null,
        public ?string $description = null,
        public ?string $externalId = null,
        public array $images = [],
        public ?CarbonImmutable $createdAt = null,
        public ?CarbonImmutable $updatedAt = null
    ) {}

    /**
     * Properties that should be cast to CarbonImmutable.
     *
     * @return array<int, string>
     */
    protected static function dates(): array
    {
        return ['createdAt', 'updatedAt'];
    }

    /**
     * Properties that should be cast to enums.
     *
     * @return array<string, class-string>
     */
    protected static function enums(): array
    {
        return [
            'documentType' => DocumentType::class,
            'status'       => DocumentStatus::class,
        ];
    }

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @return array<string, class-string<DataTransferObject>>
     */
    protected static function nestedArrays(): array
    {
        return [
            'images' => DocumentImage::class,
        ];
    }

    /**
     * Property mappings from API response keys to class properties.
     *
     * @return array<string, string>
     */
    protected static function propertyMappings(): array
    {
        return [
            'company_id'    => 'companyId',
            'driver_id'     => 'driverId',
            'document_type' => 'documentType',
            'external_id'   => 'externalId',
            'created_at'    => 'createdAt',
            'updated_at'    => 'updatedAt',
        ];
    }
}
