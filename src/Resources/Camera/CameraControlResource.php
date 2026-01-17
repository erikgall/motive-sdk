<?php

namespace Motive\Resources\Camera;

use Motive\Data\Video;
use Motive\Data\VideoRequest;
use Motive\Resources\Resource;

/**
 * Resource for camera control and video requests.
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class CameraControlResource extends Resource
{
    /**
     * Get a video by request ID.
     */
    public function getVideo(int|string $requestId): Video
    {
        $response = $this->client->get($this->fullPath("{$requestId}/video"));

        return Video::from($response->json('video'));
    }

    /**
     * Request a video recording.
     *
     * @param  array<string, mixed>  $params
     */
    public function requestVideo(array $params): VideoRequest
    {
        $response = $this->client->post($this->fullPath(), [
            'video_request' => $params,
        ]);

        return VideoRequest::from($response->json('video_request'));
    }

    protected function basePath(): string
    {
        return 'video_requests';
    }

    /**
     * @return class-string<VideoRequest>
     */
    protected function dtoClass(): string
    {
        return VideoRequest::class;
    }

    protected function resourceKey(): string
    {
        return 'video_request';
    }
}
