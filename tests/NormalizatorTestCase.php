<?php

declare(strict_types=1);

namespace Normalizator\Tests;

use Normalizator\Cache\Cache;
use Normalizator\Enum\Permissions;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\Filter\NormalizationFilterInterface;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\Normalization\NormalizationInterface;
use Normalizator\NormalizationFactory;
use Normalizator\Normalizator;
use Normalizator\NormalizatorInterface;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Logger;
use Normalizator\Util\Slugify;
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
    protected string $fixturesRoot;
    protected vfsStreamDirectory $root;

    /**
     * Set up test environment.
     */
    public function setUp(): void
    {
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
    }

    /**
     * Create a normalization filter for using in tests.
     */
    protected function createFilter(string $type): NormalizationFilterInterface
    {
        $finder = new Finder();
        $cache = new Cache();
        $gitDiscovery = new GitDiscovery();
        $filterFactory = new FilterFactory($finder, $cache, $gitDiscovery);

        return $filterFactory->make($type);
    }

    /**
     * @param array<mixed> $configuration
     */
    protected function createNormalization(string $type, array $configuration = []): NormalizationInterface
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $slugify = new Slugify();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $cache = new Cache();
        $filterFactory = new FilterFactory($finder, $cache, $gitDiscovery);

        $logger = new Logger();
        $normalizationListener = new NormalizationListener($logger);
        $listenerProvider = new ListenerProvider();
        $listenerProvider->addListener(NormalizationEvent::class, $normalizationListener);
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $factory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filterFactory,
            $eventDispatcher,
        );

        return $factory->make($type, $configuration);
    }

    protected function createNormalizator(): NormalizatorInterface
    {
        $finder = new Finder();
        $gitDiscovery = new GitDiscovery();
        $eolDiscovery = new EolDiscovery($gitDiscovery);
        $cache = new Cache();
        $filterFactory = new FilterFactory($finder, $cache, $gitDiscovery);
        $slugify = new Slugify();
        $filenameResolver = new FilenameResolver();

        $logger = new Logger();
        $normalizationListener = new NormalizationListener($logger);
        $listenerProvider = new ListenerProvider();
        $listenerProvider->addListener(NormalizationEvent::class, $normalizationListener);
        $eventDispatcher = new EventDispatcher($listenerProvider);

        $normalizationFactory = new NormalizationFactory(
            $finder,
            $slugify,
            $eolDiscovery,
            $gitDiscovery,
            $filterFactory,
            $eventDispatcher,
        );

        return new Normalizator(
            $normalizationFactory,
            $filenameResolver,
            $eventDispatcher,
            $logger,
        );
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
}
