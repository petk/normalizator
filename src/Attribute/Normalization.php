<?php

declare(strict_types=1);

namespace Normalizator\Attribute;

/**
 * Normalization attribute that sets the normalization name and its filters.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Normalization
{
    /**
     * Class constructor.
     *
     * @param array<int,string> $filters
     */
    public function __construct(
        public string $name,
        public array $filters = [],
    ) {
    }
}
