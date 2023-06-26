<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\AskForEncodingEvent;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;

use function Normalizator\mb_convert_encoding;

/**
 * Normalization that converts file to UTF-8 or ASCII encoding if possible.
 */
#[Normalization(
    name: 'encoding',
    filters: [
        'file',
        'plain_text',
        'no_git',
        'no_node_modules',
        'no_svn',
        'no_vendor',
    ]
)]
class EncodingNormalization implements NormalizationInterface, ConfigurableNormalizationInterface
{
    /**
     * List of encodings that can be converted to UTF-8 confidently.
     *
     * @var array<int,string>
     */
    private $supportedEncodings = [
        'iso-8859-2',
        'windows-1252',
    ];

    private ?\Closure $encodingCallback = null;

    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function configure(mixed ...$options): void
    {
        if (isset($options['encoding_callback']) && $options['encoding_callback'] instanceof \Closure) {
            $this->encodingCallback = $options['encoding_callback'];
        } else {
            $this->encodingCallback = null;
        }
    }

    /**
     * Normalizes file encoding.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $encoding = $this->getFileEncoding($file);

        // Encoding is ok.
        if (in_array($encoding, ['ascii', 'us-ascii', 'utf-8'], true)) {
            return $file;
        }

        // Automatic encoding normalization supports only a limited list of
        // encoding conversions. Try asking user for encoding if encoding
        // cannot be automatically converted.
        if (
            !in_array($encoding, $this->supportedEncodings, true)
            && null !== $this->encodingCallback
        ) {
            /** @var AskForEncodingEvent */
            $event = $this->eventDispatcher->dispatch(new AskForEncodingEvent($file, $encoding));
            $encoding = $event->getEncoding();
        }

        // Validate encoding input.
        $valid = false;
        foreach (\mb_list_encodings() as $supported) {
            if (strtolower($supported) === $encoding) {
                $valid = true;

                break;
            }
        }

        $encoding = ('' === $encoding) ? 'unknown' : $encoding;

        // Encoding is not valid.
        if (
            !$valid
            || (!in_array($encoding, $this->supportedEncodings, true) && null === $this->encodingCallback)
        ) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'encoding ' . $encoding, 'error'));

            return $file;
        }

        $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'encoding ' . $encoding . ' -> UTF-8'));

        return $this->convert($file, 'UTF-8', $encoding);
    }

    protected function convert(File $file, string $to, string $from): File
    {
        $content = mb_convert_encoding($file->getNewContent(), $to, $from);

        if (!is_array($content)) {
            $file->setNewContent($content);
        }

        return $file;
    }

    /**
     * Determine file encoding.
     *
     * First it tries to determine content encoding using the PHP file
     * extension, then it tries the mbstring extension. If nothing works it
     * returns an empty string.
     */
    private function getFileEncoding(File $file): string
    {
        // First resort is the file extension which might give us proper
        // encoding.
        $finfo = new \finfo(\FILEINFO_MIME_ENCODING);
        $encoding = $finfo->file($file->getPathname());

        if (false !== $encoding && !str_starts_with($encoding, 'unknown')) {
            return $encoding;
        }

        // Then, try the mbstring extension to check the content encoding
        // against the list of supported encodings.
        $encoding = \mb_detect_encoding($file->getNewContent(), \mb_list_encodings(), true);

        // UTF-8 or normal ASCII text file.
        if (in_array($encoding, ['UTF-8', 'ASCII'], true)) {
            return strtolower($encoding);
        }

        // Some known encoding.
        if (false !== $encoding) {
            return strtolower($encoding);
        }

        // Encoding couldn't be determined.
        return '';
    }
}
