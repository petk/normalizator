<?php

declare(strict_types=1);

namespace Normalizator\Exception;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
}
