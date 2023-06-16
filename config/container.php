<?php

declare(strict_types=1);

use Normalizator\Cache\Cache;
use Normalizator\ConfigurationResolver;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\Console\Command\FixCommand;
use Normalizator\Console\Command\SelfUpdateCommand;
use Normalizator\Container;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\NormalizationFactory;
use Normalizator\Normalizator;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Logger;
use Normalizator\Util\Slugify;
use Normalizator\Util\Timer;

$container = new Container();

$container->set(Timer::class, function ($c) {
    return new Timer();
});

$container->set(Finder::class, function ($c) {
    return new Finder();
});

$container->set(GitDiscovery::class, function ($c) {
    return new GitDiscovery();
});

$container->set(EolDiscovery::class, function ($c) {
    return new EolDiscovery($c->get(GitDiscovery::class));
});

$container->set(Cache::class, function ($c) {
    return new Cache();
});

$container->set(FilterFactory::class, function ($c) {
    return new FilterFactory(
        $c->get(Finder::class),
        $c->get(Cache::class),
        $c->get(GitDiscovery::class),
    );
});

$container->set(Slugify::class, function ($c) {
    return new Slugify();
});

$container->set(FilenameResolver::class, function ($c) {
    return new FilenameResolver();
});

$container->set(Logger::class, function ($c) {
    return new Logger();
});

$container->set(NormalizationListener::class, function ($c) {
    return new NormalizationListener($c->get(Logger::class));
});

$container->set(ListenerProvider::class, function ($c) {
    $provider = new ListenerProvider();
    $provider->addListener(NormalizationEvent::class, $c->get(NormalizationListener::class));

    return $provider;
});

$container->set(EventDispatcher::class, function ($c) {
    return new EventDispatcher($c->get(ListenerProvider::class));
});

$container->set(NormalizationFactory::class, function ($c) {
    return new NormalizationFactory(
        $c->get(Finder::class),
        $c->get(Slugify::class),
        $c->get(EolDiscovery::class),
        $c->get(GitDiscovery::class),
        $c->get(FilterFactory::class),
        $c->get(EventDispatcher::class),
    );
});

$container->set(Normalizator::class, function ($c) {
    return new Normalizator(
        $c->get(NormalizationFactory::class),
        $c->get(FilenameResolver::class),
        $c->get(EventDispatcher::class),
        $c->get(Logger::class),
    );
});

$container->set(ConfigurationResolver::class, function ($c) {
    return new ConfigurationResolver($c->get(EolDiscovery::class));
});

$container->set(CheckCommand::class, function ($c) {
    return new CheckCommand(
        $c->get(ConfigurationResolver::class),
        $c->get(Finder::class),
        $c->get(Normalizator::class),
        $c->get(Timer::class),
        $c->get(Logger::class),
    );
});

$container->set(FixCommand::class, function ($c) {
    return new FixCommand(
        $c->get(ConfigurationResolver::class),
        $c->get(Finder::class),
        $c->get(Normalizator::class),
        $c->get(Timer::class),
        $c->get(Logger::class),
    );
});

$container->set(SelfUpdateCommand::class, function ($c) {
    return new SelfUpdateCommand();
});

return $container;
