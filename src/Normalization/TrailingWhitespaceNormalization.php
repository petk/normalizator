<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\File;

use function Normalizator\preg_replace;

/**
 * Normalization that trims trailing whitespace characters (spaces and tabs).
 */
#[Normalization(
    name: 'trailing-whitespace',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class TrailingWhitespaceNormalization extends AbstractNormalization
{
    /**
     * Trim trailing spaces and tabs from each line.
     */
    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newContent = preg_replace('/(*BSR_ANYCRLF)[\t ]+(\R|$)/m', '$1', $content);

        if (!is_array($newContent) && $content !== $newContent) {
            $file->setNewContent($newContent);
            $this->notify($file, 'trailing whitespace');
        }

        return $file;
    }
}
