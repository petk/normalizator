<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\File;

/**
 * The leading newlines trimming utility.
 */
#[Normalization(
    name: 'leading-eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class LeadingEolNormalization extends AbstractNormalization
{
    /**
     * Trim all newlines from the beginning of the file.
     */
    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newContent = ltrim($content, "\r\n");

        if ($content !== $newContent) {
            $file->setNewContent($newContent);
            $this->notify($file, 'leading EOL(s)');
        }

        return $file;
    }
}
