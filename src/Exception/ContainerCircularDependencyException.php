<?php

declare(strict_types=1);

namespace Normalizator\Exception;

use Psr\Container\ContainerExceptionInterface;

class ContainerCircularDependencyException extends \RuntimeException implements ContainerExceptionInterface
{
}
