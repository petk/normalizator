<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\Finder\File;

/**
 * Utility that checks if file can be renamed without overwriting any existing
 * files and returns non-existing name instead.
 */
class FilenameResolver
{
    /**
     * Checks if file can be renamed and returnes resolved new filename.
     */
    public function resolve(File $file, string $newFilename): string
    {
        $newFile = new File($file->getPath() . '/' . $newFilename);

        $extension = $newFile->getExtension();
        $extension = ('' !== $extension) ? '.' . $extension : '';
        $nameWithoutExtension = $newFilename;

        if (
            '' !== $extension
            && false !== $position = strrpos($newFilename, '.')
        ) {
            $nameWithoutExtension = substr($newFilename, 0, $position);
        }

        $nameSuffixedWithoutExtension = $nameWithoutExtension;

        // Check if filename already exists.
        $suffix = 1;
        while (
            $file->getPathname() !== $file->getPath() . '/' . $nameSuffixedWithoutExtension . $extension
            && file_exists($file->getPath() . '/' . $nameSuffixedWithoutExtension . $extension)
        ) {
            $nameSuffixedWithoutExtension = $nameWithoutExtension . '_' . $suffix;
            ++$suffix;
        }

        return $nameSuffixedWithoutExtension . $extension;
    }
}
