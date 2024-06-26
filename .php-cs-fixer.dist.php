<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('tests/fixtures/generated')
    ->notPath('tests/fixtures/miscellaneous.php')
    ->in(__DIR__)
    ->name(['*.php', 'normalizator', 'build'])
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'modernize_strpos' => true,
        'no_alias_functions' => true,
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        '@PER-CS' => true,
        // Override PhpCsFixer rules.
        'native_function_invocation' => [
            'include' => [
                '@internal',
                'chmod',
                'copy',
                'exec',
                'file_get_contents',
                'file_put_contents',
                'fileperms',
                'mb_convert_encoding',
                'md5_file',
                'mime_content_type',
                'mkdir',
                'preg_match_all',
                'preg_match',
                'preg_replace_callback',
                'preg_replace',
                'preg_replace',
                'preg_split',
                'rename',
                'rmdir',
                'transliterator_transliterate',
                'unlink',
            ],
            'strict' => true,
            'scope' => 'namespaced',
        ],
        'native_constant_invocation' => [
            'fix_built_in' => true,
            'include' => [],
            'scope' => 'namespaced',
            'strict' => true,
        ],
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha'
        ],
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_to_comment' => ['ignored_tags' => ['var']],
        'phpdoc_annotation_without_dot' => false,
        'php_unit_attributes' => true,
        'php_unit_test_class_requires_covers' => false,
        'php_unit_test_case_static_method_calls' => [
            'call_type' => 'this',
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
