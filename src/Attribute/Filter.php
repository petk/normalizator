<?php

declare(strict_types=1);

namespace Normalizator\Attribute;

/**
 * Normalization filter attribute class that sets the filter name.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Filter
{
    public function __construct(public string $name)
    {
    }
}
