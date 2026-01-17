<?php

namespace Motive\Tests\Unit;

use Motive\MotiveManager;
use Motive\Client\MotiveClient;
use PHPUnit\Framework\TestCase;
use Motive\Contracts\Authenticator;
use Motive\Auth\ApiKeyAuthenticator;
use PHPUnit\Framework\Attributes\Test;

/**
 * @author Erik Galloway <egalloway@motive.com>
 */
class MotiveManagerTest extends TestCase
{
    #[Test]
    public function it_allows_custom_authenticator(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [],
        ];

        $customAuth = $this->createMock(Authenticator::class);

        $manager = new MotiveManager($config);
        $newManager = $manager->withAuthenticator($customAuth);

        $this->assertSame($customAuth, $newManager->getAuthenticator());
    }

    #[Test]
    public function it_creates_api_key_authenticator(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [],
        ];

        $manager = new MotiveManager($config);
        $authenticator = $manager->getAuthenticator();

        $this->assertInstanceOf(ApiKeyAuthenticator::class, $authenticator);
    }

    #[Test]
    public function it_creates_immutable_copy_with_api_key(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'original-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [],
        ];

        $manager = new MotiveManager($config);
        $newManager = $manager->withApiKey('new-api-key');

        $this->assertInstanceOf(MotiveManager::class, $newManager);
        $this->assertNotSame($manager, $newManager);
    }

    #[Test]
    public function it_creates_immutable_copy_with_metric_units(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [
                'metric_units' => false,
            ],
        ];

        $manager = new MotiveManager($config);
        $newManager = $manager->withMetricUnits();

        $this->assertInstanceOf(MotiveManager::class, $newManager);
        $this->assertNotSame($manager, $newManager);
    }

    #[Test]
    public function it_creates_immutable_copy_with_timezone(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [
                'timezone' => null,
            ],
        ];

        $manager = new MotiveManager($config);
        $newManager = $manager->withTimezone('America/Chicago');

        $this->assertInstanceOf(MotiveManager::class, $newManager);
        $this->assertNotSame($manager, $newManager);
    }

    #[Test]
    public function it_creates_manager_with_config(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-api-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => [
                        'times' => 3,
                        'sleep' => 100,
                    ],
                ],
            ],
            'headers' => [
                'timezone'     => null,
                'metric_units' => false,
            ],
        ];

        $manager = new MotiveManager($config);

        $this->assertInstanceOf(MotiveManager::class, $manager);
    }

    #[Test]
    public function it_gets_default_connection_client(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'test-api-key',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => [
                        'times' => 3,
                        'sleep' => 100,
                    ],
                ],
            ],
            'headers' => [],
        ];

        $manager = new MotiveManager($config);
        $client = $manager->client();

        $this->assertInstanceOf(MotiveClient::class, $client);
    }

    #[Test]
    public function it_switches_connections(): void
    {
        $config = [
            'default'     => 'default',
            'connections' => [
                'default' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'key-1',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
                'tenant-a' => [
                    'auth_driver' => 'api_key',
                    'api_key'     => 'key-2',
                    'base_url'    => 'https://api.gomotive.com',
                    'timeout'     => 30,
                    'retry'       => ['times' => 3, 'sleep' => 100],
                ],
            ],
            'headers' => [],
        ];

        $manager = new MotiveManager($config);

        $tenantManager = $manager->connection('tenant-a');

        $this->assertInstanceOf(MotiveManager::class, $tenantManager);
        $this->assertNotSame($manager, $tenantManager);
    }
}
