<?php

namespace Motive\Tests\Unit\Resources\Groups;

use Motive\Data\Group;
use Motive\Client\Response;
use Motive\Data\GroupMember;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Motive\Resources\Groups\GroupsResource;
use Illuminate\Http\Client\Response as HttpResponse;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class GroupsResourceTest extends TestCase
{
    #[Test]
    public function it_adds_member_to_group(): void
    {
        $response = $this->createMockResponse(['success' => true]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('post')
            ->with('/v1/groups/123/members', ['member_id' => 456, 'member_type' => 'driver'])
            ->willReturn($response);

        $resource = new GroupsResource($client);
        $result = $resource->addMember(123, 456, 'driver');

        $this->assertTrue($result);
    }

    #[Test]
    public function it_finds_group_by_id(): void
    {
        $groupData = [
            'id'         => 123,
            'company_id' => 456,
            'name'       => 'West Coast',
        ];

        $response = $this->createMockResponse(['group' => $groupData]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/groups/123')
            ->willReturn($response);

        $resource = new GroupsResource($client);
        $group = $resource->find(123);

        $this->assertInstanceOf(Group::class, $group);
        $this->assertSame(123, $group->id);
        $this->assertSame('West Coast', $group->name);
    }

    #[Test]
    public function it_gets_group_members(): void
    {
        $memberData = [
            'id'          => 1,
            'group_id'    => 123,
            'member_id'   => 456,
            'member_type' => 'driver',
        ];

        $response = $this->createMockResponse(['group_members' => [$memberData]]);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('get')
            ->with('/v1/groups/123/members')
            ->willReturn($response);

        $resource = new GroupsResource($client);
        $members = $resource->members(123);

        $this->assertCount(1, $members);
        $this->assertInstanceOf(GroupMember::class, $members->first());
    }

    #[Test]
    public function it_has_correct_base_path(): void
    {
        $resource = new GroupsResource($this->createStub(MotiveClient::class));

        $this->assertSame('groups', $resource->getBasePath());
    }

    #[Test]
    public function it_has_correct_resource_key(): void
    {
        $resource = new GroupsResource($this->createStub(MotiveClient::class));

        $this->assertSame('group', $resource->getResourceKey());
    }

    #[Test]
    public function it_removes_member_from_group(): void
    {
        $response = $this->createMockResponse([], 204);

        $client = $this->createMock(MotiveClient::class);
        $client->expects($this->once())
            ->method('delete')
            ->with('/v1/groups/123/members/456')
            ->willReturn($response);

        $resource = new GroupsResource($client);
        $result = $resource->removeMember(123, 456);

        $this->assertTrue($result);
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
