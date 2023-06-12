<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\Finder\File;

use function Normalizator\mb_convert_encoding;

/**
 * Normalization that converts file to UTF-8 or ASCII encoding if possible.
 */
#[Normalization(
    name: 'encoding',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class EncodingNormalization extends AbstractNormalization
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

    /**
     * Normalizes file encoding.
     */
    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        $encoding = $this->getFileEncoding($file);

        if ('' === $encoding) {
            $this->notify('unknown encoding');

            return $file;
        }

        // @todo: Here we should try to fix the encoding to proper one.
        // Options:
        // 1. iconv('ISO-8859-7', 'UTF-8', file_get_contents($fileName))
        // 2. mb_convert_encoding
        // 3. UConverter::transcode
        if (!in_array($encoding, ['ascii', 'us-ascii', 'utf-8'], true)) {
            // Encoding normalization allows only a limited list of encoding
            // conversions.
            if (!in_array($encoding, $this->supportedEncodings, true)) {
                $this->notify('encoding ' . $encoding, 'manual');

                return $file;
            }

            $this->notify('encoding ' . $encoding . ' to UTF-8');

            $content = mb_convert_encoding($file->getNewContent(), 'UTF-8', $encoding);

            if (!is_array($content)) {
                $file->setNewContent($content);
            }
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
