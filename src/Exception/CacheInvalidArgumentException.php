<?php

declare(strict_types=1);

namespace Normalizator\Exception;

use Psr\SimpleCache\InvalidArgumentException;

/**
 * Exception that is thrown when invalid key is passed to cache methods.
 */
class CacheInvalidArgumentException extends \InvalidArgumentException implements InvalidArgumentException
{
}
