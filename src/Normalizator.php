<?php

declare(strict_types=1);

namespace Normalizator;

use Normalizator\Configuration\Configuration;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\Logger;
use RuntimeException;

use function array_merge;

/**
 * Normalizes content of given file.
 */
class Normalizator implements NormalizatorInterface
{
    public const VERSION = '0.0.4';

    public function __construct(
        private Configuration $configuration,
        private NormalizationFactory $normalizationFactory,
        private FilenameResolver $filenameResolver,
        private EventDispatcher $eventDispatcher,
        private Logger $logger,
    ) {
    }

    public function normalize(File $path): void
    {
        $this->normalizeContent($path);
        $this->normalizePermissions($path);
        $this->normalizePath($path);
    }

    public function save(File $path): void
    {
        if (!$this->isNormalized($path)) {
            $this->normalize($path);
        }

        try {
            $path->save();
        } catch (RuntimeException $e) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($path, 'file ' . $path->getNewFilename() . ' could not be saved. ' . $e->getMessage(), 'error'));
        }
    }

    public function isNormalized(File $file): bool
    {
        return [] !== array_merge(
            $this->logger->getLogs($file),
            $this->logger->getErrors($file)
        );
    }

    private function normalizeContent(File $path): File
    {
        if (!$path->isFile()) {
            return $path;
        }

        if (false !== $this->configuration->get('encoding')) {
            $path = $this->normalizationFactory->make('encoding', [
                'encoding_callback' => $this->configuration->get('encoding_callback'),
            ])->normalize($path);
        }

        if (false !== $this->configuration->get('trailing_whitespace')) {
            $path = $this->normalizationFactory->make('trailing_whitespace')->normalize($path);
        }

        if (false !== $this->configuration->get('final_eol')) {
            $path = $this->normalizationFactory->make('final_eol', [
                'eol' => $this->configuration->get('eol'),
                'max_extra_final_eols' => $this->configuration->get('max_extra_final_eols'),
            ])->normalize($path);
        }

        if (false !== $this->configuration->get('eol')) {
            $path = $this->normalizationFactory->make('eol', [
                'eol' => $this->configuration->get('eol'),
                'skip_cr' => $this->configuration->get('skip_cr'),
            ])->normalize($path);
        }

        if (false !== $this->configuration->get('leading_eol')) {
            $path = $this->normalizationFactory->make('leading_eol')->normalize($path);
        }

        if (false !== $this->configuration->get('middle_eol')) {
            $path = $this->normalizationFactory->make('middle_eol', [
                'max_extra_middle_eols' => $this->configuration->get('max_extra_middle_eols'),
            ])->normalize($path);
        }

        if (false !== $this->configuration->get('space_before_tab')) {
            $path = $this->normalizationFactory->make('space_before_tab')->normalize($path);
        }

        if (false !== $this->configuration->get('indentation')) {
            $path = $this->normalizationFactory->make('indentation', [
                'indentation' => $this->configuration->get('indentation'),
                'indentation_size' => $this->configuration->get('indentation_size'),
            ])->normalize($path);
        }

        return $path;
    }

    private function normalizePath(File $path): File
    {
        if (false !== $this->configuration->get('extension')) {
            $path = $this->normalizationFactory->make('extension')->normalize($path);
        }

        if (false !== $this->configuration->get('name')) {
            $path = $this->normalizationFactory->make('name')->normalize($path);
        }

        // Check if file with such new filename already exists and resolve it.
        $previousFilename = $path->getNewFilename();
        $path = $this->filenameResolver->resolve($path);
        if ($previousFilename !== $path->getNewFilename()) {
            $this->eventDispatcher->dispatch(new NormalizationEvent($path, 'file with the same name already exists; new filename: ' . $path->getNewFilename()));
        }

        return $path;
    }

    private function normalizePermissions(File $path): File
    {
        if (false !== $this->configuration->get('permissions')) {
            $path = $this->normalizationFactory->make('permissions')->normalize($path);
        }

        return $path;
    }
}
