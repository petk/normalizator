<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Exception;

use function preg_match;
use function preg_replace_callback;
use function restore_error_handler;
use function set_error_handler;

use const E_WARNING;

/**
 * Converter and checker for glob patterns.
 */
class Glob
{
    /**
     * Check if given string is glob pattern.
     */
    public function isGlob(string $string): bool
    {
        return 1 === preg_match('/(?<!\\\)[\*\?\{\}\[\]]/', $string);
    }

    /**
     * Convert given glob pattern to regular expression for using in PHP preg_*
     * functions.
     */
    public function convertToRegex(string $string): string
    {
        $regex = preg_replace_callback('/(?<!\\\)[\*\?\{\}\[\]\.]/', static function ($matches) {
            switch ($matches[0]) {
                case '*':
                    return '.*';

                case '?':
                    return '.';

                case '.':
                    return '\.';

                default:
                    return '\\' . $matches[0];
            }
        }, $string);

        $regex = '/' . $regex . '/';

        set_error_handler(static function () {}, E_WARNING);
        $isRegularExpression = false !== preg_match($regex, '');
        restore_error_handler();
        if ($isRegularExpression) {
            return $regex;
        }

        throw new Exception('Given pattern could not be converted to regular expression.');
    }
}
