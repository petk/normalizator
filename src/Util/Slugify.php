<?php

declare(strict_types=1);

namespace Normalizator\Util;

use RuntimeException;

use function array_keys;
use function array_values;
use function is_array;
use function Normalizator\preg_replace;
use function Normalizator\transliterator_transliterate;
use function str_replace;
use function trim;

/**
 * Slugify utility for normalizations.
 */
class Slugify
{
    /**
     * Array of simple replacements.
     *
     * @var array<string,string>
     */
    private array $replacements = [
        'đ' => 'dz',
        'Đ' => 'DZ',
        ';' => '-',
        ',' => '-',
        '#' => '-',
    ];

    /**
     * Array of regex replacements.
     *
     * @var array<string,string>
     */
    private array $patterns = [
        // Replace all occurrences of spaces + ampersand (&) + spaces with a
        // single ampersand &.
        '/\s*\&\s*/' => '&',

        // Replace all occurrences of spaces and dashes with a single dash.
        '/[-\s]+/' => '-',
    ];

    /**
     * Slugify method.
     *
     * @throws RuntimeException
     */
    public function slugify(string $string): string
    {
        // Replace all occurrences of simple replacements.
        foreach ($this->replacements as $search => $replace) {
            $string = str_replace($search, $replace, $string);
        }

        $transliterated = transliterator_transliterate('Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC;', $string);

        // Replace all patterns.
        $transliterated = preg_replace(array_keys($this->patterns), array_values($this->patterns), $transliterated);

        if (is_array($transliterated)) {
            throw new RuntimeException('Something got wrong when replacing patterns');
        }

        // Remove dashes from the beginning or the end.
        return trim($transliterated, '-');
    }
}
