<?php

/**
 * EOL fixtures.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'eol' => [
            'lf' => [],
            'crlf' => [],
        ]
    ],
    'fixed' => [
        'eol' => [
            'lf' => [],
            'crlf' => [],
        ]
    ]
];

// LF files.
$structure['initial']['eol']['lf']['file_1.txt'] = "lorem ipsum\ndolor\nsit\namet\n";
$structure['fixed']['eol']['lf']['file_1.txt'] = "lorem ipsum\ndolor\nsit\namet\n";

$structure['initial']['eol']['lf']['file_2.txt'] = "lorem ipsum\r\ndolor\r\nsit\r\namet\r\n";
$structure['fixed']['eol']['lf']['file_2.txt'] = "lorem ipsum\ndolor\nsit\namet\n";

$structure['initial']['eol']['lf']['file_3.txt'] = "lorem ipsum\r\ndolor\nsit\namet\r\n";
$structure['fixed']['eol']['lf']['file_3.txt'] = "lorem ipsum\ndolor\nsit\namet\n";

// CRLF files.
$structure['initial']['eol']['crlf']['file_1.txt'] = "lorem ipsum\ndolor\nsit\namet\n";
$structure['fixed']['eol']['crlf']['file_1.txt'] = "lorem ipsum\r\ndolor\r\nsit\r\namet\r\n";

$structure['initial']['eol']['crlf']['file_2.txt'] = "lorem ipsum\r\ndolor\r\nsit\r\namet\r\n";
$structure['fixed']['eol']['crlf']['file_2.txt'] = "lorem ipsum\r\ndolor\r\nsit\r\namet\r\n";

$structure['initial']['eol']['crlf']['file_3.txt'] = "lorem ipsum\r\ndolor\nsit\namet\r\n";
$structure['fixed']['eol']['crlf']['file_3.txt'] = "lorem ipsum\r\ndolor\r\nsit\r\namet\r\n";

return $structure;
