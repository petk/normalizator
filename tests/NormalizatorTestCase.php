<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Container;
use Normalizator\Enum\Permissions;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\FilterFactory;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\NormalizationFactory;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class NormalizatorTestCase extends TestCase
{
    protected Container $container;
    protected string $fixturesRoot;
    protected vfsStreamDirectory $root;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
        $this->container = require __DIR__ . '/../config/container.php';

        $this->fixturesRoot = __DIR__ . '/fixtures';

        $this->root = vfsStream::setup('tests');
        vfsStream::copyFromFileSystem($this->fixturesRoot);

        // Set some permissions on some files.
        $file = $this->root->getChild('initial/permissions/Rakefile');
        $file->chmod(Permissions::FILE->get());
        $file = $this->root->getChild('initial/permissions/not-a-script.sh');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script-2');
        $file->chmod(Permissions::EXECUTABLE->get());
        $file = $this->root->getChild('initial/permissions/shell-script-3');
        $file->chmod(Permissions::EXECUTABLE->get());

        $this->addWhitespaceFiles();
        $this->addEolFiles();
        $this->addFinalEolFiles();
        $this->addIndentationFiles();
    }

    /**
     * Create a normalization filter for using in tests.
     */
    protected function createFilter(string $type): NormalizationFilterInterface
    {
        /** @var FilterFactory */
        $factory = $this->container->get(FilterFactory::class);

        return $factory->make($type);
    }

    /**
     * @param array<mixed> $configuration
     */
    protected function createNormalization(string $type, array $configuration = []): NormalizationInterface
    {
        /** @var NormalizationFactory */
        $factory = $this->container->get(NormalizationFactory::class);

        return $factory->make($type, $configuration);
    }

    private function addWhitespaceFiles(): void
    {
        // Add no-break space.
        $file = vfsStream::newFile('initial/trailing-whitespace/no-break-space.txt');
        $file->setContent("\u{00A0}");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/no-break-space.txt');
        $this->root->addChild($file);

        // Add Mongolian vowel separator.
        $file = vfsStream::newFile('initial/trailing-whitespace/mongolian-vowel-separator.txt');
        $file->setContent("\u{180E}");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/mongolian-vowel-separator.txt');
        $this->root->addChild($file);

        // Add en quad.
        $file = vfsStream::newFile('initial/trailing-whitespace/en-quad.txt');
        $file->setContent("\u{2000}\n\u{2000}\u{2000}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/en-quad.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add em quad.
        $file = vfsStream::newFile('initial/trailing-whitespace/em-quad.txt');
        $file->setContent("\u{2001}\n\u{2001}\u{2001}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/em-quad.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add en space.
        $file = vfsStream::newFile('initial/trailing-whitespace/en-space.txt');
        $file->setContent("\u{2002}\n\u{2002}\u{2002}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/en-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add em space.
        $file = vfsStream::newFile('initial/trailing-whitespace/em-space.txt');
        $file->setContent("\u{2003}\n\u{2003}\u{2003}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/em-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add three-per-em space
        $file = vfsStream::newFile('initial/trailing-whitespace/three-per-em-space.txt');
        $file->setContent("\u{2004}\n\u{2004}\u{2004}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/three-per-em-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add four-per-em space
        $file = vfsStream::newFile('initial/trailing-whitespace/four-per-em-space.txt');
        $file->setContent("\u{2005}\n\u{2005}\u{2005}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/four-per-em-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add six-per-em space
        $file = vfsStream::newFile('initial/trailing-whitespace/six-per-em-space.txt');
        $file->setContent("\u{2006}\n\u{2006}\u{2006}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/six-per-em-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add figure space.
        $file = vfsStream::newFile('initial/trailing-whitespace/figure-space.txt');
        $file->setContent("\u{2007}\n\u{2007}\u{2007}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/figure-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add punctuation space.
        $file = vfsStream::newFile('initial/trailing-whitespace/punctuation-space.txt');
        $file->setContent("\u{2008}\n\u{2008}\u{2008}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/punctuation-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add thin space.
        $file = vfsStream::newFile('initial/trailing-whitespace/thin-space.txt');
        $file->setContent("\u{2009}\n\u{2009}\u{2009}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/thin-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add thin space.
        $file = vfsStream::newFile('initial/trailing-whitespace/hair-space.txt');
        $file->setContent("\u{200A}\n\u{200A}\u{200A}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/hair-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add narrow no-break space.
        $file = vfsStream::newFile('initial/trailing-whitespace/narrow-no-break-space.txt');
        $file->setContent("\u{202F}\n\u{202F}\u{202F}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/narrow-no-break-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add medium mathematical space.
        $file = vfsStream::newFile('initial/trailing-whitespace/medium-mathematical-space.txt');
        $file->setContent("\u{205F}\n\u{205F}\u{205F}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/medium-mathematical-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add ideographic space.
        $file = vfsStream::newFile('initial/trailing-whitespace/ideographic-space.txt');
        $file->setContent("\u{3000}\n\u{3000}\u{3000}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/ideographic-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add zero width space.
        $file = vfsStream::newFile('initial/trailing-whitespace/zero-width-space.txt');
        $file->setContent("\u{200B}\n\u{200B}\u{200B}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/zero-width-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Add zero width no-break space.
        $file = vfsStream::newFile('initial/trailing-whitespace/zero-width-no-break-space.txt');
        $file->setContent("\u{FEFF}\n\u{FEFF}\u{FEFF}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/zero-width-no-break-space.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        // Whitespace various.
        $file = vfsStream::newFile('initial/trailing-whitespace/various.txt');
        $file->setContent("README\u{00A0}\u{200B}\u{202F}\u{FEFF}\u{180E}\u{3000}\u{205F}\u{2008}\n\nLorem ipsum\u{2009}\u{00A0}\u{200A}\u{2001}\ndolor sit amet  \n\n\u{00A0}\n\t\t \nfoobar\u{00A0}\u{180E}  \n\u{0080}\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/trailing-whitespace/various.txt');
        $file->setContent("README\n\nLorem ipsum\ndolor sit amet\n\n\n\nfoobar\n\u{0080}\n");
        $this->root->addChild($file);
    }

    private function addEolFiles(): void
    {
        // LF files.
        $file = vfsStream::newFile('initial/eol/lf/file_1.txt');
        $file->setContent("lorem ipsum\ndolor\nsit\namet\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/lf/file_1.txt');
        $file->setContent("lorem ipsum\ndolor\nsit\namet\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/eol/lf/file_2.txt');
        $file->setContent("lorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/lf/file_2.txt');
        $file->setContent("lorem ipsum\ndolor\nsit\namet\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/eol/lf/file_3.txt');
        $file->setContent("lorem ipsum\r\ndolor\nsit\namet\r\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/lf/file_3.txt');
        $file->setContent("lorem ipsum\ndolor\nsit\namet\n");
        $this->root->addChild($file);

        // CRLF files.
        $file = vfsStream::newFile('initial/eol/crlf/file_1.txt');
        $file->setContent("lorem ipsum\ndolor\nsit\namet\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/crlf/file_1.txt');
        $file->setContent("lorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/eol/crlf/file_2.txt');
        $file->setContent("lorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/crlf/file_2.txt');
        $file->setContent("lorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/eol/crlf/file_3.txt');
        $file->setContent("lorem ipsum\r\ndolor\nsit\namet\r\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/eol/crlf/file_3.txt');
        $file->setContent("lorem ipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);
    }

    private function addFinalEolFiles(): void
    {
        $file = vfsStream::newFile('initial/final-eol-2/file-1.txt');
        $file->setContent("\r\r\rlorem ipsum");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-1.txt');
        $file->setContent("\r\r\rlorem ipsum\r");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-2.txt');
        $file->setContent("\n\n\nlorem ipsum");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-2.txt');
        $file->setContent("\n\n\nlorem ipsum\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-3.txt');
        $file->setContent("\r\n\r\nlorem ipsum");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-3.txt');
        $file->setContent("\r\n\r\nlorem ipsum\r\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-4.txt');
        $file->setContent("\n\nlorem ipsum\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-4.txt');
        $file->setContent("\n\nlorem ipsum\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-5.txt');
        $file->setContent("\n\nlorem ipsum\n\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-5.txt');
        $file->setContent("\n\nlorem ipsum\n\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-6.txt');
        $file->setContent("\n\nlorem ipsum\n\n\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-6.txt');
        $file->setContent("\n\nlorem ipsum\n\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-7.txt');
        $file->setContent("\n\n\n\n\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-7.txt');
        $file->setContent("\n\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-8.txt');
        $file->setContent("\n\n  \n\n\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-8.txt');
        $file->setContent("\n\n  \n\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-9.txt');
        $file->setContent("\r\n\r\n  \r\n\r\n\r\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-9.txt');
        $file->setContent("\r\n\r\n  \r\n\r\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-2/file-10.txt');
        $file->setContent('lorem ipsum dolor sit amet');
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-2/file-10.txt');
        $file->setContent("lorem ipsum dolor sit amet\n");
        $this->root->addChild($file);

        // CRLF files.
        $file = vfsStream::newFile('initial/final-eol-crlf/file_1.txt');
        $file->setContent("lorem\r\nipsum\r\ndolor\r\nsit\r\namet");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-crlf/file_1.txt');
        $file->setContent("lorem\r\nipsum\r\ndolor\r\nsit\r\namet\r\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/final-eol-crlf/file_2.txt');
        $file->setContent('lorem ipsum dolor sit amet');
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/final-eol-crlf/file_2.txt');
        $file->setContent("lorem ipsum dolor sit amet\r\n");
        $this->root->addChild($file);
    }

    private function addIndentationFiles(): void
    {
        // --indentation=space --indentation-size=4
        $file = vfsStream::newFile('initial/indentation/file_1.txt');
        $file->setContent("    README\n\tLorem ipsum dolor sit amet.\n\n    \tLorem ipsum dolor sit amet,\n\nconsectetur adipiscing elit\n\t\t    \t    \t    sed do eiusmod tempor\n                        incididunt ut\n\t\t\t\t\t\tlabore et dolore magna aliqua.\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_1.txt');
        $file->setContent("    README\n    Lorem ipsum dolor sit amet.\n\n        Lorem ipsum dolor sit amet,\n\nconsectetur adipiscing elit\n                            sed do eiusmod tempor\n                        incididunt ut\n                        labore et dolore magna aliqua.\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_2.txt');
        $file->setContent("# README\n\t## About\n\n\t\tLorem ipsum dolor sit amet,\n\n\t\t\tconsectetur adipiscing elit,\n\n\t\tsed do eiusmod tempor incididunt\n\t");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_2.txt');
        $file->setContent("# README\n    ## About\n\n        Lorem ipsum dolor sit amet,\n\n            consectetur adipiscing elit,\n\n        sed do eiusmod tempor incididunt\n    ");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_3.txt');
        $file->setContent("README\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_3.txt');
        $file->setContent("README\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_4.txt');
        $file->setContent('');
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_4.txt');
        $file->setContent('');
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_5.txt');
        $file->setContent("    README\n\n    Lorem ipsum dolor sit amet.\n");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_5.txt');
        $file->setContent("    README\n\n    Lorem ipsum dolor sit amet.\n");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_6.txt');
        $file->setContent("\tREADME");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_6.txt');
        $file->setContent('    README');
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_7.txt');
        $file->setContent("\tREADME\r\n\t\tLorem ipsum\r\n\r\n\t\t    \t    \tdolor sit\r\n\t");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_7.txt');
        $file->setContent("    README\r\n        Lorem ipsum\r\n\r\n                        dolor sit\r\n    ");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation/file_8.txt');
        $file->setContent("\tREADME\r\t\tLorem ipsum\r\r\t\t    \t    \tdolor sit\r\t");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation/file_8.txt');
        $file->setContent("    README\r        Lorem ipsum\r\r                        dolor sit\r    ");
        $this->root->addChild($file);

        // --indentation=space --indentation-size=2
        $file = vfsStream::newFile('initial/indentation-2/file_1.txt');
        $file->setContent("\tREADME");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation-2/file_1.txt');
        $file->setContent('  README');
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation-2/file_2.txt');
        $file->setContent("\tREADME\r\n\t\tLorem ipsum\r\n\r\n\t\t    \t    \tdolor sit\r\n\t");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation-2/file_2.txt');
        $file->setContent("  README\r\n    Lorem ipsum\r\n\r\n                dolor sit\r\n  ");
        $this->root->addChild($file);

        $file = vfsStream::newFile('initial/indentation-2/file_3.txt');
        $file->setContent("\tREADME\r\t\tLorem ipsum\r\r\t\t    \t    \tdolor sit\r\t");
        $this->root->addChild($file);
        $file = vfsStream::newFile('fixed/indentation-2/file_3.txt');
        $file->setContent("  README\r    Lorem ipsum\r\r                dolor sit\r  ");
        $this->root->addChild($file);
    }
}
