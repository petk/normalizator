<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;

use function Normalizator\preg_match;

/**
 * Must be plain-text file.
 */
#[Filter(
    name: 'plain-text'
)]
class PlainTextFilter implements NormalizationFilterInterface
{
    public function filter(File $file): bool
    {
        $mimeType = $file->getMimeType();

        if ('inode/x-empty' === $mimeType) {
            return true;
        }

        return 1 === preg_match('/^text\//', $mimeType);
    }
}
