<?php

declare(strict_types=1);

namespace Normalizator\Enum;

/**
 * Default permissions enumerator.
 */
enum Permissions
{
    // Default directoy permissions.
    case DIRECTORY;

    // Default executable files permissions.
    case EXECUTABLE;

    // Default file permissions.
    case FILE;

    /*
     * Default stricter permissions for protected files (such as files in Git
     * objects directory).
     */
    case FILE_PROTECTED;

    /**
     * Get default permissions for enums.
     *
     * Based on the system's umask the default permissions can be off by 002.
     */
    public function get(): int
    {
        if (002 === umask()) {
            $directory = 0775;
            $executable = 0775;
            $file = 0664;
        } else {
            $directory = 0755;
            $executable = 0755;
            $file = 0644;
        }

        $protected = 0444;

        return match ($this) {
            Permissions::DIRECTORY => $directory,
            Permissions::EXECUTABLE => $executable,
            Permissions::FILE => $file,
            Permissions::FILE_PROTECTED => $protected,
        };
    }
}
