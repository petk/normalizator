<?php

declare(strict_types=1);

namespace Normalizator\Normalization;

use Normalizator\Attribute\Normalization;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\Finder\File;
use Normalizator\Util\EolDiscovery;

/**
 * Normalizes final newlines.
 */
#[Normalization(
    name: 'final-eol',
    filters: [
        'file',
        'plain-text',
        'no-git',
        'no-node-modules',
        'no-svn',
        'no-vendor',
    ]
)]
class FinalEolNormalization extends AbstractNormalization implements ConfigurableNormalizationInterface
{
    /**
     * @var array<string,mixed>
     */
    protected array $configuration = [
        'max' => 1,
    ];

    /**
     * Class constructor.
     */
    public function __construct(
        private EventDispatcher $eventDispatcher,
        private EolDiscovery $eolDiscovery
    ) {
    }

    /**
     * Insert one missing final newline at the end of the string, using a
     * prevailing EOL from the given string - LF (\n), CRLF (\r\n) or CR (\r).
     */
    public function normalize(File $file): File
    {
        if (!$this->filter($file)) {
            return $file;
        }

        $content = $file->getNewContent();

        $newlines = $this->getFinalEols($content);
        $trimmed = rtrim($content, "\r\n");

        $max = $this->configuration['max'];

        // Empty content doesn't need one final newline when max = 1
        if ('' === $trimmed && 1 === $max) {
            $max = 0;
        }

        for ($i = 0; $i < $max; ++$i) {
            if (empty($newlines[$i])) {
                break;
            }

            $trimmed .= $newlines[$i];
        }

        // Then insert one missing final EOL if not present yet.
        if (!in_array(substr($trimmed, -1), ["\n", "\r"], true)) {
            $trimmed .= $this->eolDiscovery->getPrevailingEol($content);
        }

        if ($content !== $trimmed) {
            $file->setNewContent($trimmed);
            $this->eventDispatcher->dispatch(new NormalizationEvent($file, count($newlines) . ' final EOL(s)'));
        }

        return $file;
    }

   /**
    * Get all final newlines.
    *
    * @return array<int,string>
    */
   private function getFinalEols(string $content): array
   {
       $newlines = [];

       while ('' !== $content) {
           if ("\r\n" === substr($content, -2)) {
               $newlines[] = "\r\n";
               $content = substr($content, 0, -2);

               continue;
           }

           if ("\n" === substr($content, -1)) {
               $newlines[] = "\n";
               $content = substr($content, 0, -1);

               continue;
           }

           if ("\r" === substr($content, -1)) {
               $newlines[] = "\r";
               $content = substr($content, 0, -1);

               continue;
           }

           break;
       }

       return array_reverse($newlines);
   }
}
