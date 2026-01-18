<?php

namespace Motive\Tests\Unit\Resources\Documents;

use Motive\Data\Document;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Enums\DocumentStatus;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Documents\DocumentsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class DocumentsResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new DocumentsResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/documents', $resource->fullPath());
        $this->assertSame('/v1/documents/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_deletes_document(): void
    {
        $response = $this->createStub(Response::class);
        $response->method('successful')->willReturn(true);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/documents/123')
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_downloads_document(): void
    {
        $pdfContent = '%PDF-1.4 mock content';

        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('body')->willReturn($pdfContent);
        $httpResponse->method('successful')->willReturn(true);
        $httpResponse->method('status')->willReturn(200);

        $response = new Response($httpResponse);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/documents/123/download')
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $content = $resource->download(123);

        $this->assertSame($pdfContent, $content);
    }

    #[Test]
    public function it_finds_document_by_id(): void
    {
        $documentData = [
            'id'            => 123,
            'company_id'    => 456,
            'driver_id'     => 789,
            'document_type' => 'delivery_receipt',
            'status'        => 'approved',
            'description'   => 'Delivery confirmation',
        ];

        $response = $this->createMockResponse(['document' => $documentData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/documents/123')
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $document = $resource->find(123);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame(123, $document->id);
        $this->assertSame(456, $document->companyId);
        $this->assertSame(789, $document->driverId);
        $this->assertSame('Delivery confirmation', $document->description);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new DocumentsResource($this->createStub(MotiveClient::class));

        $this->assertSame('documents', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new DocumentsResource($this->createStub(MotiveClient::class));

        $this->assertSame('document', $resource->getResourceKey());
    }

    #[Test]
    public function it_lists_documents(): void
    {
        $documentsData = [
            [
                'id'            => 123,
                'company_id'    => 456,
                'document_type' => 'bill_of_lading',
                'status'        => 'pending',
            ],
            [
                'id'            => 124,
                'company_id'    => 456,
                'document_type' => 'fuel_receipt',
                'status'        => 'approved',
            ],
        ];

        $response = $this->createMockResponse([
            'documents'  => $documentsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $client = $this->createStub(MotiveClient::class);
        $client->method('get')->willReturn($response);

        $resource = new DocumentsResource($client);
        $documents = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $documents);

        $documentsArray = $documents->all();
        $this->assertCount(2, $documentsArray);
        $this->assertInstanceOf(Document::class, $documentsArray[0]);
        $this->assertSame(123, $documentsArray[0]->id);
    }

    #[Test]
    public function it_lists_documents_for_driver(): void
    {
        $documentsData = [
            [
                'id'            => 123,
                'company_id'    => 456,
                'driver_id'     => 789,
                'document_type' => 'fuel_receipt',
                'status'        => 'pending',
            ],
        ];

        $response = $this->createMockResponse([
            'documents'  => $documentsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/documents', ['driver_id' => 789, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $documents = $resource->forDriver(789);

        $this->assertInstanceOf(LazyCollection::class, $documents);
        $documentsArray = $documents->all();
        $this->assertCount(1, $documentsArray);
        $this->assertSame(789, $documentsArray[0]->driverId);
    }

    #[Test]
    public function it_updates_document_status(): void
    {
        $documentData = [
            'id'            => 123,
            'company_id'    => 456,
            'document_type' => 'bill_of_lading',
            'status'        => 'approved',
        ];

        $response = $this->createMockResponse(['document' => $documentData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/documents/123', ['document' => ['status' => 'approved']])
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $document = $resource->updateStatus(123, DocumentStatus::Approved);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame(DocumentStatus::Approved, $document->status);
    }

    #[Test]
    public function it_uploads_document(): void
    {
        $documentData = [
            'id'            => 125,
            'company_id'    => 456,
            'driver_id'     => 789,
            'document_type' => 'bill_of_lading',
            'status'        => 'pending',
            'external_id'   => 'BOL-2024-001',
        ];

        $response = $this->createMockResponse(['document' => $documentData], 201);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/documents', ['document' => ['driver_id' => 789, 'document_type' => 'bill_of_lading', 'external_id' => 'BOL-2024-001']])
            ->willReturn($response);

        $resource = new DocumentsResource($client);
        $document = $resource->upload([
            'driver_id'     => 789,
            'document_type' => 'bill_of_lading',
            'external_id'   => 'BOL-2024-001',
        ]);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertSame(125, $document->id);
        $this->assertSame('BOL-2024-001', $document->externalId);
    }

    /**
     * Create a mock Response with JSON data.
     *
     * @param  array<string, mixed>  $data
     */
    private function createMockResponse(array $data, int $status = 200): Response
    {
        $httpResponse = $this->createStub(HttpResponse::class);
        $httpResponse->method('json')->willReturnCallback(
            fn (?string $key = null) => $key !== null ? ($data[$key] ?? null) : $data
        );
        $httpResponse->method('status')->willReturn($status);
        $httpResponse->method('successful')->willReturn($status >= 200 && $status < 300);

        return new Response($httpResponse);
    }
}
