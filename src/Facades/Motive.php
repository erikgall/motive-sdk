<?php

namespace Motive\Facades;

use Motive\MotiveManager;
use Motive\Client\MotiveClient;
use Motive\Contracts\Authenticator;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Motive SDK.
 *
 * @method static MotiveClient client()
 * @method static Authenticator getAuthenticator()
 * @method static MotiveManager connection(string $name)
 * @method static MotiveManager withApiKey(string $apiKey)
 * @method static MotiveManager withAuthenticator(Authenticator $authenticator)
 * @method static MotiveManager withTimezone(string $timezone)
 * @method static MotiveManager withMetricUnits(bool $enabled = true)
 * @method static MotiveManager withUserId(int|string $userId)
 *
 * @see \Motive\MotiveManager
 *
 * @author Erik Galloway <egalloway@motive.com>
 */
class Motive extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MotiveManager::class;
    }
}
