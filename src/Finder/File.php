<?php

declare(strict_types=1);

namespace Normalizator\Finder;

use RuntimeException;
use SplFileInfo;

use function array_map;
use function explode;
use function file_exists;
use function implode;
use function is_file;
use function Normalizator\chmod;
use function Normalizator\file_get_contents;
use function Normalizator\file_put_contents;
use function Normalizator\fileperms;
use function Normalizator\mime_content_type;
use function Normalizator\rename;
use function pathinfo;
use function str_replace;
use function substr;

use const PATHINFO_EXTENSION;

/**
 * File wrapper which extends from the \SplFileInfo.
 *
 * It adds some more functionality on top of \SplFileInfo and creates a
 * simple repository for some file values and their manipulation.
 */
class File extends SplFileInfo
{
    /**
     * The relative path of the file based on the directory passed to finder.
     */
    private string $subPathname;

    /**
     * Root path of the file. This is the directory given to Finder or the
     * parent path of the file if Finder wasn't used.
     */
    private string $rootPath;

    private string $content;
    private string $newContent;
    private string $newFilename;
    private ?string $extension = null;
    private int $newPermissions;

    public function __construct(string $pathname, string $subPathname = null, string $rootPath = null)
    {
        parent::__construct($pathname);

        if (isset($subPathname)) {
            $this->subPathname = $subPathname;
        }

        if (isset($rootPath)) {
            $this->rootPath = $rootPath;
        }
    }

    /**
     * Get content of the current file.
     */
    public function getContent(): string
    {
        if (isset($this->content)) {
            return $this->content;
        }

        return $this->content = file_get_contents($this->getEncodedPathname());
    }

    /**
     * Get new content of the current file.
     */
    public function getNewContent(): string
    {
        if (isset($this->newContent)) {
            return $this->newContent;
        }

        $content = $this->getContent();

        return $this->newContent = $content;
    }

    /**
     * Set new content.
     *
     * When doing normalizations on the conten, this is used to update the file
     * contents and still keep the original content via the getContent() method.
     */
    public function setNewContent(string $content): void
    {
        $this->newContent = $content;
    }

    /**
     * This sets the new filename and updates the extension.
     */
    public function setNewFilename(string $filename): void
    {
        $this->extension = null;
        $this->newFilename = $filename;
        $this->extension = $this->getExtension();
    }

    public function getNewFilename(): string
    {
        return $this->newFilename ?? $this->getFilename();
    }

    public function setNewPermissions(int $permissions): void
    {
        $this->newPermissions = $permissions;
    }

    public function getNewPermissions(): int
    {
        return $this->newPermissions ?? $this->getPerms();
    }

    /**
     * Customized permissions getter for easier working with permissions within
     * this app.
     */
    public function getPerms(): int
    {
        // Special case for virtual file system.
        if ('vfs://' === substr($this->getEncodedPathname(), 0, 6)) {
            return fileperms($this->getEncodedPathname()) & 0777;
        }

        return parent::getPerms() & 0777;
    }

    /**
     * Save file with new content, new permissions and new filename on disk.
     *
     * @throws RuntimeException
     */
    public function save(): void
    {
        if ($this->isFile() && $this->hasContentChanged()) {
            file_put_contents($this->getEncodedPathname(), $this->getNewContent());
        }

        if (!$this->isLink() && $this->hasPermissionsChanged()) {
            chmod($this->getEncodedPathname(), $this->getNewPermissions());
        }

        if ($this->hasFilenameChanged()) {
            $newFile = $this->getPath() . '/' . $this->getNewFilename();

            // Safety when renaming a file to existing one so it doesn't overwrite it.
            if (file_exists($newFile)) {
                throw new RuntimeException('Attempting to rename ' . $this->getPathname() . ' to ' . $newFile . ' which already exists on disk.');
            }

            rename($this->getEncodedPathname(), $newFile);
        }
    }

    /**
     * Guess current file MIME type.
     */
    public function getMimeType(): string
    {
        return mime_content_type($this->getEncodedPathname());
    }

    /**
     * Overridden extension getter which gets extension from the new filename.
     *
     * Additionally, it returns extension in a bit more advanced mode than the
     * usual core method.
     */
    public function getExtension(): string
    {
        if (isset($this->extension)) {
            return $this->extension;
        }

        $extension = pathinfo($this->getNewFilename(), PATHINFO_EXTENSION);

        if ('tar.gz' === substr($this->getNewFilename(), 0, -6)) {
            $extension = 'tar.gz';
        }

        return $this->extension = $extension;
    }

    public function isFile(): bool
    {
        return is_file($this->getEncodedPathname());
    }

    /**
     * When file is retrieved from iterator as part of the finder class, this
     * will return only the relative path from the finder path location.
     */
    public function getSubPathname(): string
    {
        return $this->subPathname ?? $this->getPathname();
    }

    /**
     * Get root path of the file.
     *
     * If Finder was used, this is the directory passed to Finder's getTree()
     * method otherwise, the parent folder of the given file is returned.
     */
    public function getRootPath(): string
    {
        return $this->rootPath ?? $this->getPath();
    }

    private function hasContentChanged(): bool
    {
        return $this->getContent() !== $this->getNewContent();
    }

    private function hasPermissionsChanged(): bool
    {
        return $this->getPerms() !== $this->getNewPermissions();
    }

    private function hasFilenameChanged(): bool
    {
        return $this->getFilename() !== $this->getNewFilename();
    }

    /**
     * Return a pathname with encoded parts between directory separators "/".
     *
     * For special cases where we're using a virtual file system, spaces
     * and other special characters wouldn't work properly for some functions
     * such as permissions, mime types and similar. So we encode the path parts.
     */
    private function getEncodedPathname(): string
    {
        $pathname = $this->getPathname();

        if ('vfs://' === substr($pathname, 0, 6)) {
            $url = substr($pathname, 6);
            $url = implode('/', array_map('rawurlencode', explode('/', str_replace('\\', '/', $url))));

            return 'vfs://' . $url;
        }

        return $pathname;
    }
}
