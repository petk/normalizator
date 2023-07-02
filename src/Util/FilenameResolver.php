<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Normalizator\Finder\File;

use function file_exists;
use function strrpos;
use function substr;

/**
 * Utility that checks if file can be renamed without overwriting any existing
 * files and returns non-existing name instead.
 */
class FilenameResolver
{
    /**
     * Checks if file can be renamed and returnes resolved new filename.
     */
    public function resolve(File $file): File
    {
        $newFile = new File($file->getPath() . '/' . $file->getNewFilename());

        $extension = $newFile->getExtension();
        $extension = ('' !== $extension) ? '.' . $extension : '';
        $nameWithoutExtension = $file->getNewFilename();

        if (
            '' !== $extension
            && false !== $position = strrpos($file->getNewFilename(), '.')
        ) {
            $nameWithoutExtension = substr($file->getNewFilename(), 0, $position);
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

        $file->setNewFilename($nameSuffixedWithoutExtension . $extension);

        return $file;
    }
}
