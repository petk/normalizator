<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

use function implode;
use function is_array;
use function Normalizator\preg_replace;

/**
 * Normalization that trims trailing whitespace characters.
 */
#[Normalization(
    name: 'trailing_whitespace',
    filters: [
        'file',
        'plain_text',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ],
)]
class TrailingWhitespaceNormalization implements NormalizationInterface
{
    /**
     * List of supported whitespace characters.
     *
     * Some unicode whitespace characters are encoded in UTF-8 representation
     * for easier handling inside the preg_* function.
     *
     * @var array<int,string>
     */
    protected array $whitespace = [
        // Space U+0020
        ' ',
        // Tab U+000B
        '\t',
        // No-break space U+00A0
        '\xc2\xa0',
        // Mongolian vowel separator U+180E
        '\xe1\xa0\x8e',
        // En quad U+2000
        '\xe2\x80\x80',
        // Em quad U+2001
        '\xe2\x80\x81',
        // En space U+2002
        '\xe2\x80\x82',
        // Em space U+2003
        '\xe2\x80\x83',
        // Three-per-em space U+2004
        '\xe2\x80\x84',
        // Four-per-em space U+2005
        '\xe2\x80\x85',
        // Six-per-em space U+2006
        '\xe2\x80\x86',
        // Figure space U+2007
        '\xe2\x80\x87',
        // Punctuation space U+2008
        '\xe2\x80\x88',
        // Thin space U+2009
        '\xe2\x80\x89',
        // Hair space U+200A
        '\xe2\x80\x8a',
        // Narrow no-break space U+202F
        '\xe2\x80\xaf',
        // Medium mathematical space U+205F
        '\xe2\x81\x9f',
        // Ideographic space U+3000
        '\xe3\x80\x80',
        // Zero width space U+200B
        '\xe2\x80\x8b',
        // Zero width no-break space U+FEFF
        '\xef\xbb\xbf',
    ];

    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
    ) {}

    /**
     * Trim trailing whitespace characters from each line.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newContent = preg_replace('/(*BSR_ANYCRLF)(' . implode('|', $this->whitespace) . ')+(\R|$)/m', '$2', $content);

        if (!is_array($newContent) && $content !== $newContent) {
            $file->setNewContent($newContent);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'trailing whitespace'));
        }

        return $file;
    }
}
