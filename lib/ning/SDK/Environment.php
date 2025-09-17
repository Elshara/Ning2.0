<?php
namespace Ning\SDK;

use Ning\SDK\Entities\Profile;
use Ning\SDK\Services\ApplicationService;
use Ning\SDK\Services\ProfileService;
use Ning\SDK\Services\ContentService;
use Ning\SDK\Services\QueryService;
use Ning\SDK\Services\CacheService;
use Ning\SDK\Services\RestService;

/**
 * Service locator for the lightweight Ning compatibility layer.
 */
class Environment
{
    private static $bootstrapped = false;
    private static $applicationService;
    private static $profileService;
    private static $contentService;
    private static $queryService;
    private static $cacheService;
    private static $restService;

    /**
     * Initialise the environment and wire services together.
     *
     * @param array $config Optional configuration overrides.
     */
    public static function bootstrap(array $config = []): void
    {
        $applicationConfig = $config['application'] ?? [];
        self::$applicationService = new ApplicationService($applicationConfig);
        self::$cacheService = new CacheService();
        self::$contentService = new ContentService();
        self::$queryService = new QueryService(self::$contentService);
        $profileConfig = $config['profiles'] ?? [];
        $profileClass = class_exists('\\XN_Profile') ? '\\XN_Profile' : Profile::class;
        self::$profileService = new ProfileService(self::$applicationService, $profileConfig, $profileClass);
        self::$restService = new RestService();
        self::$bootstrapped = true;

        \XN_Profile::setService(self::$profileService);
        \XN_Content::setService(self::$contentService);
        \XN_Query::setService(self::$queryService);
        \XN_Cache::setService(self::$cacheService);
        \XN_REST::setService(self::$restService);
    }

    public static function reset(array $config = []): void
    {
        self::bootstrap($config);
    }

    private static function ensureBootstrapped(): void
    {
        if (!self::$bootstrapped) {
            self::bootstrap();
        }
    }

    public static function application(): ApplicationService
    {
        self::ensureBootstrapped();
        return self::$applicationService;
    }

    public static function profiles(): ProfileService
    {
        self::ensureBootstrapped();
        return self::$profileService;
    }

    public static function content(): ContentService
    {
        self::ensureBootstrapped();
        return self::$contentService;
    }

    public static function query(): QueryService
    {
        self::ensureBootstrapped();
        return self::$queryService;
    }

    public static function cache(): CacheService
    {
        self::ensureBootstrapped();
        return self::$cacheService;
    }

    public static function rest(): RestService
    {
        self::ensureBootstrapped();
        return self::$restService;
    }
}
