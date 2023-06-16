<?php

declare(strict_types=1);

namespace Normalizator\Exception;

use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
