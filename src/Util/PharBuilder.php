<?php

declare(strict_types=1);

namespace Normalizator\Util;

use BadMethodCallException;
use Normalizator\Enum\Permissions;
use Normalizator\Finder\File;
use Normalizator\Finder\Finder;
use Phar;
use RecursiveIteratorIterator;
use Symfony\Component\Console\Output\OutputInterface;

use function file_exists;
use function in_array;
use function is_array;
use function Normalizator\chmod;
use function Normalizator\preg_replace;
use function Normalizator\unlink;

/**
 * The Compiler class compiles the normalizator tool.
 */
class PharBuilder
{
    public function __construct(private Finder $finder) {}

    /**
     * @throws BadMethodCallException
     */
    public function build(string $pharFile = 'normalizator.phar', ?OutputInterface $output = null): void
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new Phar($pharFile);

        $phar->setSignatureAlgorithm(Phar::SHA1);

        $phar->startBuffering();

        $phar->setStub($this->getStub());

        $this->addNormalizator($phar);

        $this->addContainer($phar);

        // CLI Component files.

        // Add src files
        $files = $this->finder->getTree(__DIR__ . '/../../src/', static function (File $file) {
            if (in_array($file->getFilename(), ['BuildCommand.php', 'PharBuilder.php'], true)) {
                return false;
            }

            return true;
        }, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $file) {
            $path = 'src/' . $file->getSubPathname();

            if (null !== $output) {
                $output->writeln('Adding <comment>' . $path . '</comment>');
            }

            $phar->addFromString($path, $file->getContent());
        }

        // Add vendor files
        $vendor = $this->finder->getTree(__DIR__ . '/../../vendor/', static function (File $file) {
            if ($file->isDir() || in_array($file->getExtension(), ['php', 'bash', 'fish', 'zsh'], true)) {
                return true;
            }

            return false;
        }, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($vendor as $file) {
            $path = 'vendor/' . $file->getSubPathname();

            if (null !== $output) {
                $output->writeln('Adding <comment>' . $path . '</comment>');
            }

            $phar->addFromString($path, $file->getContent());
        }

        $phar->stopBuffering();

        $phar->compressFiles(Phar::GZ);

        unset($phar);
        chmod($pharFile, Permissions::EXECUTABLE->get());
    }

    /**
     * Remove the shebang from the file before add it to the PHAR file.
     */
    protected function addNormalizator(Phar $phar): void
    {
        $file = new File(__DIR__ . '/../../bin/normalizator');

        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $file->getContent());

        if (!is_array($content)) {
            $phar->addFromString('normalizator', $content);
        }
    }

    /**
     * Add dependency injection container configuration file.
     */
    protected function addContainer(Phar $phar): void
    {
        // Add container configuration.
        $containerFile = new File(__DIR__ . '/../../config/container.php');

        $phar->addFromString('config/container.php', $containerFile->getContent());
    }

    protected function getStub(): string
    {
        return "#!/usr/bin/env php\n<?php Phar::mapPhar('normalizator.phar'); require 'phar://normalizator.phar/normalizator'; __HALT_COMPILER();";
    }
}
