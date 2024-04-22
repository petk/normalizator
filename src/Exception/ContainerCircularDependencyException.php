<?php

declare(strict_types=1);

namespace Normalizator\Exception;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class ContainerCircularDependencyException extends RuntimeException implements ContainerExceptionInterface {}
