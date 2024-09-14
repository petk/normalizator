<?php

declare(strict_types=1);

use Normalizator\Cache\Cache;
use Normalizator\Configuration\Configuration;
use Normalizator\Configuration\ConfigurationResolver;
use Normalizator\Configuration\Configurator;
use Normalizator\Console\Command\CheckCommand;
use Normalizator\Console\Command\FixCommand;
use Normalizator\Console\Command\SelfUpdateCommand;
use Normalizator\Container;
use Normalizator\EventDispatcher\Event\AskForEncodingEvent;
use Normalizator\EventDispatcher\Event\DebugEvent;
use Normalizator\EventDispatcher\Event\NormalizationEvent;
use Normalizator\EventDispatcher\EventDispatcher;
use Normalizator\EventDispatcher\Listener\DebugListener;
use Normalizator\EventDispatcher\Listener\NormalizationListener;
use Normalizator\EventDispatcher\ListenerProvider;
use Normalizator\Filter\FilterManager;
use Normalizator\FilterFactory;
use Normalizator\Finder\Finder;
use Normalizator\NormalizationFactory;
use Normalizator\Normalizator;
use Normalizator\Util\ApiClient;
use Normalizator\Util\EolDiscovery;
use Normalizator\Util\FilenameResolver;
use Normalizator\Util\GitDiscovery;
use Normalizator\Util\Glob;
use Normalizator\Util\Logger;
use Normalizator\Util\Slugify;
use Normalizator\Util\Timer;

$container = new Container();

$container->set(Timer::class, static function ($c) {
    return new Timer();
});

$container->set(Logger::class, static function ($c) {
    return new Logger();
});

$container->set(Configuration::class, static function ($c) {
    return new Configuration();
});

$container->set(NormalizationListener::class, static function ($c) {
    return new NormalizationListener(
        $c->get(Logger::class),
        $c->get(Configuration::class),
    );
});

$container->set(DebugListener::class, static function ($c) {
    return new DebugListener($c->get(Logger::class));
});

$container->set(ListenerProvider::class, static function ($c) {
    $provider = new ListenerProvider();

    $provider->addListener(
        NormalizationEvent::class,
        $c->get(NormalizationListener::class),
    );

    $provider->addListener(
        AskForEncodingEvent::class,
        $c->get(NormalizationListener::class),
    );

    $provider->addListener(
        DebugEvent::class,
        $c->get(DebugListener::class),
    );

    return $provider;
});

$container->set(EventDispatcher::class, static function ($c) {
    return new EventDispatcher($c->get(ListenerProvider::class));
});

$container->set(Cache::class, static function ($c) {
    return new Cache();
});

$container->set(GitDiscovery::class, static function ($c) {
    return new GitDiscovery(
        $c->get(Cache::class),
    );
});

$container->set(EolDiscovery::class, static function ($c) {
    return new EolDiscovery(
        $c->get(EventDispatcher::class),
        $c->get(GitDiscovery::class),
        $c->get(Cache::class),
    );
});

$container->set(Finder::class, static function ($c) {
    return new Finder();
});

$container->set(FilterFactory::class, static function ($c) {
    return new FilterFactory(
        $c->get(Finder::class),
        $c->get(Cache::class),
        $c->get(GitDiscovery::class),
    );
});

$container->set(Slugify::class, static function ($c) {
    return new Slugify();
});

$container->set(FilterManager::class, static function ($c) {
    return new FilterManager($c->get(FilterFactory::class));
});

$container->set(NormalizationFactory::class, static function ($c) {
    return new NormalizationFactory(
        $c->get(Finder::class),
        $c,
    );
});

$container->set(FilenameResolver::class, static function ($c) {
    return new FilenameResolver();
});

$container->set(Normalizator::class, static function ($c) {
    return new Normalizator(
        $c->get(Configuration::class),
        $c->get(NormalizationFactory::class),
        $c->get(FilenameResolver::class),
        $c->get(EventDispatcher::class),
        $c->get(Logger::class),
    );
});

$container->set(ConfigurationResolver::class, static function ($c) {
    return new ConfigurationResolver();
});

$container->set(Configurator::class, static function ($c) {
    return new Configurator(
        $c->get(Configuration::class),
        $c->get(ConfigurationResolver::class),
    );
});

$container->set(CheckCommand::class, static function ($c) {
    return new CheckCommand(
        $c->get(Configurator::class),
        $c->get(Finder::class),
        $c->get(Normalizator::class),
        $c->get(Timer::class),
        $c->get(Logger::class),
        $c->get(Glob::class),
    );
});

$container->set(FixCommand::class, static function ($c) {
    return new FixCommand(
        $c->get(Configurator::class),
        $c->get(Configuration::class),
        $c->get(Finder::class),
        $c->get(Normalizator::class),
        $c->get(Timer::class),
        $c->get(Logger::class),
        $c->get(Glob::class),
    );
});

$container->set(ApiClient::class, static function ($c) {
    return new ApiClient();
});

$container->set(SelfUpdateCommand::class, static function ($c) {
    return new SelfUpdateCommand($c->get(ApiClient::class));
});

$container->set(Glob::class, static function ($c) {
    return new Glob();
});

return $container;
