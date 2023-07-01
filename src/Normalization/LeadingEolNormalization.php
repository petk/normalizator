<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

/**
 * The leading newlines trimming utility.
 */
#[Normalization(
    name: 'leading_eol',
    filters: [
        'file',
        'plain_text',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ]
)]
class LeadingEolNormalization implements NormalizationInterface
{
    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher
    ) {
    }

    /**
     * Trim all newlines from the beginning of the file.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newContent = ltrim($content, "\r\n");

        if ($content !== $newContent) {
            $file->setNewContent($newContent);
            $this->eventDispatcher->dispatch(new NormalizationEvent(
                $file,
                sprintf(
                    '%d leading EOL%s',
                    \count($this->getLeadingEols($content)),
                    (1 === \count($this->getLeadingEols($content))) ? '' : 's'
                )
            ));
        }

        return $file;
    }

    /**
     * Get all leading newlines.
     *
     * @return array<int,string>
     */
    private function getLeadingEols(string $content): array
    {
        $newlines = [];

        while ('' !== $content) {
            if ("\r\n" === substr($content, 0, 2)) {
                $newlines[] = "\r\n";
                $content = substr($content, 2);

                continue;
            }

            if ("\n" === substr($content, 0, 1)) {
                $newlines[] = "\n";
                $content = substr($content, 1);

                continue;
            }

            if ("\r" === substr($content, 0, 1)) {
                $newlines[] = "\r";
                $content = substr($content, 1);

                continue;
            }

            break;
        }

        return array_reverse($newlines);
    }
}
