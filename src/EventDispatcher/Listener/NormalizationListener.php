<?php

declare(strict_types=1);

namespace Normalizator\EventDispatcher\Listener;

use Normalizator\Configuration\Configuration;
use Normalizator\EventDispatcher\Event\AskForEncodingEvent;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\Util\Logger;

class NormalizationListener
{
    public function __construct(
        private Logger $logger,
        private Configuration $configuration,
    ) {}

    public function __invoke(object $event): void
    {
        if ($event instanceof NormalizationEvent) {
            $this->logger->add($event->getFile(), $event->getMessage(), $event->getType());
        }

        if ($event instanceof AskForEncodingEvent) {
            $callback = $this->configuration->get('encoding_callback');

            if (null !== $callback) {
                /** @var callable $callback */
                $encoding = $callback($event->getFile(), $event->getDefaultEncoding());
                $event->setEncoding($encoding);
            }
        }
    }
}
