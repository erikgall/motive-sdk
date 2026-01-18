<?php

namespace Motive\Data;

use Carbon\CarbonImmutable;
use Motive\Enums\DocumentType;
use Motive\Enums\DocumentStatus;

/**
 * Document data transfer object.
 *
 * @author Erik Galloway <egalloway@motive.com>
 *
 * @property int $id
 * @property int $companyId
 * @property DocumentType $documentType
 * @property DocumentStatus $status
 * @property int|null $driverId
 * @property string|null $description
 * @property string|null $externalId
 * @property array<int, DocumentImage> $images
 * @property CarbonImmutable|null $createdAt
 * @property CarbonImmutable|null $updatedAt
 */
class Document extends DataTransferObject
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string, class-string|string>
     */
    protected array $casts = [
        'id'           => 'int',
        'companyId'    => 'int',
        'driverId'     => 'int',
        'documentType' => DocumentType::class,
        'status'       => DocumentStatus::class,
        'createdAt'    => CarbonImmutable::class,
        'updatedAt'    => CarbonImmutable::class,
    ];

    /**
     * Properties that should be cast to arrays of DTOs.
     *
     * @var array<string, class-string<DataTransferObject>>
     */
    protected array $nestedArrays = [
        'images' => DocumentImage::class,
    ];
}
