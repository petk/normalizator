<?php

declare(strict_types=1);

namespace Normalizator;

/**
 * Here some of the default PHP functions are overwritten so they throw proper
 * exceptions instead of returning false or null in case of failure.
 */

/**
 * Overridden \file_get_contents() function that throws exception in case of
 * failure instead of default false.
 *
 * @throws \RuntimeException
 */
function file_get_contents(string $file): string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $content = \file_get_contents($file);
    } finally {
        restore_error_handler();
    }

    if (false === $content) {
        throw new \RuntimeException($error);
    }

    return $content;
}

/**
 * Overridden \rename() function that throws exception in case of failure instead
 * of returning false.
 *
 * @param resource $context
 *
 * @throws \RuntimeException
 */
function rename(string $from, string $to, $context = null): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \rename($from, $to, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \file_put_contents() function that throws exception in case of
 * failure instead of default false.
 *
 * @param resource $context
 *
 * @throws \RuntimeException
 */
function file_put_contents(string $filename, mixed $data, int $flags = 0, $context = null): int
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \file_put_contents($filename, $data, $flags, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \chmod() function that throws exception in case of a failure
 * instead of a default false.
 *
 * @throws \RuntimeException
 */
function chmod(string $filename, int $permissions): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \chmod($filename, $permissions);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \preg_replace() function which will throw exception in case of a
 * failure instead of the default null.
 *
 * @param array<int|string,string>|string $pattern
 * @param array<int,string>|string        $replacement
 * @param array<int,string>|string        $subject
 *
 * @return array<int,string>|string
 *
 * @throws \RuntimeException
 */
function preg_replace(array|string $pattern, array|string $replacement, array|string $subject, int $limit = -1, int &$count = null): array|string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \preg_replace($pattern, $replacement, $subject, $limit, $count);
    } finally {
        restore_error_handler();
    }

    if (null === $result) {
        throw new \RuntimeException(preg_last_error_msg() . ' ' . $error);
    }

    return $result;
}

/**
 * Overriden \preg_match() function which will throw exception in case of a
 * failure instead of the default false.
 *
 * @param array<int,string> $matches
 *
 * @throws \RuntimeException
 */
function preg_match(string $pattern, string $subject, array &$matches = null, int $flags = 0, int $offset = 0): int
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \preg_match($pattern, $subject, $matches, $flags, $offset);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException(preg_last_error_msg() . ' ' . $error);
    }

    return $result;
}

/**
 * Overridden \preg_match_all() function that throws exception in case of
 * failure instead of default false.
 *
 * @param array<int,mixed> $matches
 *
 * @throws \RuntimeException
 */
function preg_match_all(string $pattern, string $subject, array &$matches = null, int $flags = 0, int $offset = 0): int
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \preg_match_all($pattern, $subject, $matches, $flags, $offset);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException(preg_last_error_msg() . ' ' . $error);
    }

    return $result;
}

/**
 * Overridden \preg_split() function that throws exception in case of failure
 * instead of the default false.
 *
 * @return array<int,string>
 */
function preg_split(string $pattern, string $subject, int $limit = -1, int $flags = 0): array
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \preg_split($pattern, $subject, $limit, $flags);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException(preg_last_error_msg() . ' ' . $error);
    }

    return $result;
}

/**
 * Overridden \transliterator_transliterate() function which throws exception
 * instead of returning false in case of a failure.
 *
 * @throws \RuntimeException
 */
function transliterator_transliterate(\Transliterator|string $transliterator, string $string, int $start = 0, int $end = -1): string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \transliterator_transliterate($transliterator, $string, $start, $end);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \mime_content_type() function that throws exception instead of
 * default false in case of failure.
 *
 * @throws \RuntimeException
 */
function mime_content_type(string $filename): string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \mime_content_type($filename);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \mb_convert_encoding() function that throws exception in case of
 * failure instead of the default false.
 *
 * @param array<int,string>|string $string
 * @param array<int,string>|string $from
 *
 * @return array<int,string>|string
 *
 * @throws \RuntimeException
 */
function mb_convert_encoding(array|string $string, string $to, null|array|string $from = null): array|string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        if (null !== $from) {
            $result = \mb_convert_encoding($string, $to, $from);
        } else {
            $result = \mb_convert_encoding($string, $to);
        }
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden exec() function that throws exception in case of failure instead
 * of default false.
 *
 * @param array<int,string> $output
 *
 * @throws \RuntimeException
 */
function exec(string $command, ?array &$output = null, ?int &$resultCode = null): string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \exec($command, $output, $resultCode);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \unlink() function that throws exception in case of failure
 * instead of default false.
 *
 * @param resource $context
 *
 * @throws \RuntimeException
 */
function unlink(string $filename, $context = null): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \unlink($filename, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \copy() function that throws exception in case of failure instead
 * of default false.
 *
 * @param resource $context
 *
 * @throws \RuntimeException
 */
function copy(string $from, string $to, $context = null): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \copy($from, $to, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \md5_file() function that throws exception in case of failure
 * instead of default false.
 *
 * @throws \RuntimeException
 */
function md5_file(string $filename, bool $binary = false): string
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \md5_file($filename, $binary);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \rmdir() function that throws exception in case of failure instead
 * of default false.
 *
 * @param resource $context
 */
function rmdir(string $directory, $context = null): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \rmdir($directory, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}

/**
 * Overridden \mkdir() function that throws exception in case of failure instead
 * of default false.
 *
 * @param resource $context
 */
function mkdir(string $directory, int $permissions = 0777, bool $recursive = false, $context = null): bool
{
    $error = '';
    set_error_handler(function (int $type, string $message) use (&$error): bool {
        $error = $message;

        return true;
    });

    try {
        $result = \mkdir($directory, $permissions, $recursive, $context);
    } finally {
        restore_error_handler();
    }

    if (false === $result) {
        throw new \RuntimeException($error);
    }

    return $result;
}
