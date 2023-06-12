<?php

declare(strict_types=1);

namespace Normalizator\Filter;

use Normalizator\Attribute\Filter;
use Normalizator\Finder\File;
use Normalizator\Util\GitDiscovery;

/**
 * Filter which doesn't pass Git directory or bare Git directory.
 */
#[Filter(
    name: 'no-git'
)]
class NoGitFilter implements NormalizationFilterInterface
{
    public function __construct(private GitDiscovery $gitDiscovery)
    {
    }

    public function filter(File $file): bool
    {
        return !$this->gitDiscovery->isInGit($file);
    }
}
