<?php

namespace Motive\Tests\Unit\Resources\Forms;

use Motive\Data\Form;
use Motive\Client\Response;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\LazyCollection;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Forms\FormsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class FormsResourceTest extends TestCase
{
    #[Test]
    public function it_builds_correct_full_path(): void
    {
        $resource = new FormsResource($this->createStub(MotiveClient::class));

        $this->assertSame('/v1/company_forms', $resource->fullPath());
        $this->assertSame('/v1/company_forms/123', $resource->fullPath('123'));
    }

    #[Test]
    public function it_creates_a_form(): void
    {
        $data = [
            'name'        => 'New Form',
            'description' => 'A new form',
            'active'      => true,
        ];

        $formData = [
            'id'          => 456,
            'company_id'  => 100,
            'name'        => 'New Form',
            'description' => 'A new form',
            'active'      => true,
        ];

        $response = $this->createMockResponse(['form' => $formData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/company_forms', ['form' => $data])
            ->willReturn($response);

        $resource = new FormsResource($client);
        $form = $resource->create($data);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertSame(456, $form->id);
        $this->assertSame('New Form', $form->name);
    }

    #[Test]
    public function it_deletes_a_form(): void
    {
        $response = $this->createMockResponse([], 204);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/company_forms/123')
            ->willReturn($response);

        $resource = new FormsResource($client);
        $result = $resource->delete(123);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_a_form(): void
    {
        $formData = [
            'id'          => 123,
            'company_id'  => 100,
            'name'        => 'Delivery Form',
            'description' => 'Form for delivery confirmation',
            'active'      => true,
        ];

        $response = $this->createMockResponse(['form' => $formData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/company_forms/123')
            ->willReturn($response);

        $resource = new FormsResource($client);
        $form = $resource->find(123);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertSame(123, $form->id);
        $this->assertSame('Delivery Form', $form->name);
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new FormsResource($this->createStub(MotiveClient::class));

        $this->assertSame('company_forms', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new FormsResource($this->createStub(MotiveClient::class));

        $this->assertSame('form', $resource->getResourceKey());
    }

    #[Test]
    public function it_lists_active_forms(): void
    {
        $formsData = [
            [
                'id'         => 1,
                'company_id' => 100,
                'name'       => 'Active Form',
                'active'     => true,
            ],
        ];

        $response = $this->createMockResponse([
            'forms'      => $formsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 1],
        ]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/company_forms', ['active' => true, 'page_no' => 1, 'per_page' => 25])
            ->willReturn($response);

        $resource = new FormsResource($client);
        $forms = $resource->active();

        $this->assertInstanceOf(LazyCollection::class, $forms);

        $formsArray = $forms->all();
        $this->assertCount(1, $formsArray);
        $this->assertTrue($formsArray[0]->active);
    }

    #[Test]
    public function it_lists_forms(): void
    {
        $formsData = [
            [
                'id'         => 1,
                'company_id' => 100,
                'name'       => 'Delivery Form',
                'active'     => true,
            ],
            [
                'id'         => 2,
                'company_id' => 100,
                'name'       => 'Inspection Form',
                'active'     => true,
            ],
        ];

        $response = $this->createMockResponse([
            'forms'      => $formsData,
            'pagination' => ['per_page' => 25, 'page_no' => 1, 'total' => 2],
        ]);

        $client = $this->createStub(MotiveClient::class);
        $client->method('get')->willReturn($response);

        $resource = new FormsResource($client);
        $forms = $resource->list();

        $this->assertInstanceOf(LazyCollection::class, $forms);

        $formsArray = $forms->all();
        $this->assertCount(2, $formsArray);
        $this->assertInstanceOf(Form::class, $formsArray[0]);
        $this->assertSame('Delivery Form', $formsArray[0]->name);
    }

    #[Test]
    public function it_updates_a_form(): void
    {
        $data = ['name' => 'Updated Form Name'];

        $formData = [
            'id'          => 123,
            'company_id'  => 100,
            'name'        => 'Updated Form Name',
            'description' => 'Original description',
            'active'      => true,
        ];

        $response = $this->createMockResponse(['form' => $formData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('patch')
            ->with('/v1/company_forms/123', ['form' => $data])
            ->willReturn($response);

        $resource = new FormsResource($client);
        $form = $resource->update(123, $data);

        $this->assertInstanceOf(Form::class, $form);
        $this->assertSame('Updated Form Name', $form->name);
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
