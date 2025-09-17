<?php
/**
 * Minimal NF utility class used by the simplified WWF bootstrap.
 */
class NF
{
    /** Identifier used by cache invalidation helpers. */
    public const INVALIDATE_ALL = '__WWF_INVALIDATE_ALL__';

    /** Constant checked before timing output is emitted. */
    public const NF_DISPLAY_TIMING = 'WWF_DISPLAY_TIMING';

    /**
     * Records timing information; in the lightweight framework we simply
     * return the current microtime so callers can compute deltas if desired.
     */
    public static function outputTime($message, $start = null)
    {
        return microtime(true);
    }

    /**
     * Fallback exception logger used throughout the legacy code base.
     */
    public static function logException($exception): void
    {
        if ($exception instanceof Throwable) {
            error_log($exception->getMessage());
        } else {
            error_log((string) $exception);
        }
    }
}
