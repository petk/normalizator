<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

use function implode;
use function in_array;
use function is_array;
use function is_int;
use function Normalizator\preg_match_all;
use function Normalizator\preg_replace_callback;
use function str_repeat;
use function str_replace;
use function substr_count;

/**
 * Normalization that trims trailing whitespace characters.
 */
#[Normalization(
    name: 'indentation',
    filters: [
        'file',
        'plain_text',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ]
)]
class IndentationNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    public const INDENTATION = ' ';
    public const INDENTATION_SIZE = 4;

    private string $indentation;
    private int $indentationSize = self::INDENTATION_SIZE;

    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function configure(mixed ...$options): void
    {
        if (
            isset($options['indentation'])
            && in_array($options['indentation'], [' ', "\t"], true)
        ) {
            $this->indentation = $options['indentation'];
        } else {
            $this->indentation = self::INDENTATION;
        }

        if (
            isset($options['indentation_size'])
            && is_int($options['indentation_size'])
        ) {
            $this->indentationSize = $options['indentation_size'];
        } else {
            $this->indentationSize = self::INDENTATION_SIZE;
        }
    }

    /**
     * Normalize mixed indentation style.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $regex = '/(*BSR_ANYCRLF)(*ANYCRLF)^([ \t]*)(.*)(\R|$)/m';

        preg_match_all($regex, $content, $matches);

        $indentation = implode('', $matches[1]);

        $hasSpaces = (0 < substr_count($indentation, ' ')) ? true : false;
        $hasTabs = (0 < substr_count($indentation, "\t")) ? true : false;

        if ($hasSpaces && $hasTabs) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'mixed indentation'));
        }

        if ($hasSpaces && !$hasTabs && "\t" === $this->indentation) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'space indentation', 'error'));
        }

        if ($hasTabs && !$hasSpaces && ' ' === $this->indentation) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'tab indentation'));
        }

        $newContent = $content;
        if ($hasTabs && ' ' === $this->indentation) {
            $newContent = preg_replace_callback($regex, function ($matches) {
                return str_replace(
                    "\t",
                    str_repeat(' ', $this->indentationSize),
                    $matches[1]
                ) . $matches[2] . $matches[3];
            }, $content);
        }

        if (!is_array($newContent) && $newContent !== $content) {
            $file->setNewContent($newContent);
        }

        return $file;
    }
}
