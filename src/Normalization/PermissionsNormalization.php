<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Exception;
use Normalizator\Attribute\Normalization;
use Normalizator\Enum\Permissions;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Filter\FilterManager;
use Normalizator\Finder\File;
use Normalizator\Util\GitDiscovery;
use Phar;

use function decoct;
use function Normalizator\preg_match;
use function trim;

/**
 * Utility that checks and sets permissions according to a predefined sets.
 */
#[Normalization(
    name: 'permissions',
    filters: [
        'no_links',
    ]
)]
class PermissionsNormalization implements NormalizationInterface
{
    public function __construct(
        private FilterManager $filterManager,
        private EventDispatcher $eventDispatcher,
        private GitDiscovery $gitDiscovery,
    ) {
    }

    /**
     * Normalizes permissions for a given path.
     */
    public function normalize(File $file): File
    {
        if (!$this->filterManager->filter($this, $file)) {
            return $file;
        }

        $permissions = $file->getPerms();

        $newPermissions = $this->getPermissions($file);

        if ($permissions !== $newPermissions) {
            $file->setNewPermissions($newPermissions);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, 'permissions from ' . decoct($permissions) . ' to ' . decoct($newPermissions)));
        }

        return $file;
    }

    private function getPermissions(File $file): int
    {
        // Set directory default permissions.
        if ($file->isDir()) {
            return Permissions::DIRECTORY->get();
        }

        // Check if file is inside .git/objects directory
        if ($this->isGitObject($file->getPathname())) {
            return Permissions::FILE_PROTECTED->get();
        }

        // Check if file is inside .git/hooks directory
        if ($this->isGitHook($file->getPathname())) {
            return Permissions::EXECUTABLE->get();
        }

        // If file is special in the gitweb directory.
        if ($this->isGitWebConfigFile($file->getPathname())) {
            return Permissions::FILE->get();
        }

        // Check if file is executable shell script.
        if ($this->isExecutableScript($file)) {
            return Permissions::EXECUTABLE->get();
        }

        return Permissions::FILE->get();
    }

    /**
     * Check if given path is within the .git/objects directory.
     *
     * Files in this directory should have 0444 permissions.
     */
    private function isGitObject(string $path): bool
    {
        // Non-bare Git repository.
        if (1 === preg_match('/.*\/\.git\/objects\/.+$/', $path)) {
            return true;
        }

        // Bare Git repository.
        preg_match('/(.*)\/objects\/.+$/', $path, $matches);

        if (isset($matches[1])) {
            return $this->gitDiscovery->isBareGitRepo($matches[1]);
        }

        return false;
    }

    /**
     * Check if given path is a hook file within the .git/hooks directory.
     *
     * Git hooks are executable script files.
     */
    private function isGitHook(string $path): bool
    {
        if (1 === preg_match('/.*\/\.git\/hooks\/.+$/', $path)) {
            return true;
        }

        // Bare Git repository.
        preg_match('/(.*)\/hooks\/.+$/', $path, $matches);

        if (isset($matches[1])) {
            return $this->gitDiscovery->isBareGitRepo($matches[1]);
        }

        return false;
    }

    private function isGitWebConfigFile(string $path): bool
    {
        if (1 === preg_match('/.*\/\.git\/gitweb\/gitweb_config\.perl$/', $path)) {
            return true;
        }

        // Bare Git repository.
        preg_match('/.*\/gitweb\/gitweb_config\.perl$/', $path, $matches);

        if (isset($matches[1])) {
            return $this->gitDiscovery->isBareGitRepo($matches[1]);
        }

        return false;
    }

    /**
     * Check if given file should be executable script.
     *
     * Mime types starting with "text/x-" usually designate executable scripts
     * but some are false positives, such as Rakefile and similar. These files
     * also need to have proper shebang in the beginning of the file. However,
     * some files have proper shebangs in them and aren't noted as executable
     * scripts.
     */
    private function isExecutableScript(File $file): bool
    {
        $mimeType = $file->getMimeType();

        // Check if given file is a phar executable script.
        if (
            'application/octet-stream' === $mimeType
            && 1 === preg_match('/^#![ \t]*\/usr\/bin\/env[ \t]?/', $file->getContent())
        ) {
            try {
                new Phar($file->getPathname());
            } catch (Exception $e) {
                return false;
            }

            return true;
        }

        // Allow only text files.
        if (1 !== preg_match('/^text\//', $mimeType)) {
            return false;
        }

        $shebangs = [
            '/^#![ \t]*\/bin\/sh[ \t]?/',
            '/^#![ \t]*\/bin\/bash[ \t]?/',
            '/^#![ \t]*\/usr\/bin\/env[ \t]?/',
        ];

        $content = trim($file->getContent());

        foreach ($shebangs as $regex) {
            if (1 === preg_match($regex, $content)) {
                return true;
            }
        }

        return false;
    }
}
