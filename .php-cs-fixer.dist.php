<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('tests/fixtures')
    ->in(__DIR__)
    ->name(['*.php', 'normalizator', 'build'])
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PER' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'modernize_strpos' => true,
        'no_alias_functions' => true,
        '@PhpCsFixer' => true,
        // Override PhpCsFixer rules.
        'concat_space' => false,
        'phpdoc_to_comment' => ['ignored_tags' => ['var']]
    ])
    ->setFinder($finder)
;
