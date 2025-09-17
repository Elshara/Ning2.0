<?php
/**
 * Minimal controller base with just enough behaviour for test execution.
 */
class NF_Controller
{
    public const CACHE_AT_LOCATION = 'cache_at_location';

    protected static bool $boundaryCommentsHidden = true;

    /**
     * The layout/rendering instructions set by actions. In the simplified
     * implementation we only honour a subset of the historic behaviour.
     */
    protected $_disposition = null;

    public static function hideBoundaryComments(): void
    {
        self::$boundaryCommentsHidden = true;
    }

    public static function showBoundaryComments(): void
    {
        self::$boundaryCommentsHidden = false;
    }

    public static function boundaryCommentsHidden(): bool
    {
        return self::$boundaryCommentsHidden;
    }

    public static function invalidateCache($id): void
    {
        // Cache invalidation is a no-op in the lightweight bootstrap.  The
        // method exists purely to satisfy legacy callers.
    }
}
