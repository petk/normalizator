<?php

/**
 * Final EOL fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'final-eol' => [],
        'final-eol-2' => [],
        'final-eol-crlf' => [],
    ],
    'fixed' => [
        'final-eol' => [],
        'final-eol-2' => [],
        'final-eol-crlf' => [],
    ],
];

// --final-eol
$structure['initial']['final-eol']['file_1.txt'] = "\nlorem ipsum";
$structure['fixed']['final-eol']['file_1.txt'] = "\nlorem ipsum\n";

$structure['initial']['final-eol']['file_2.txt'] = "\n\n\nlorem ipsum";
$structure['fixed']['final-eol']['file_2.txt'] = "\n\n\nlorem ipsum\n";

$structure['initial']['final-eol']['file_3.txt'] = "\r\nlorem ipsum";
$structure['fixed']['final-eol']['file_3.txt'] = "\r\nlorem ipsum\r\n";

$structure['initial']['final-eol']['file_4.txt'] = "\r\n\r\nlorem ipsum\r\n";
$structure['fixed']['final-eol']['file_4.txt'] = "\r\n\r\nlorem ipsum\r\n";

$structure['initial']['final-eol']['file_5.txt'] = "\r\n\r\nlorem ipsum\r\n\r\n\r\n";
$structure['fixed']['final-eol']['file_5.txt'] = "\r\n\r\nlorem ipsum\r\n";

$structure['initial']['final-eol']['file_6.txt'] = "\n\nlorem ipsum\r\n";
$structure['fixed']['final-eol']['file_6.txt'] = "\n\nlorem ipsum\r\n";

$structure['initial']['final-eol']['file_7.txt'] = "\r\n\nlorem ipsum";
$structure['fixed']['final-eol']['file_7.txt'] = "\r\n\nlorem ipsum\r\n";

$structure['initial']['final-eol']['file_8.txt'] = "\r\r\rlorem ipsum";
$structure['fixed']['final-eol']['file_8.txt'] = "\r\r\rlorem ipsum\r";

$structure['initial']['final-eol']['file_9.txt'] = "\n\n\n";
$structure['fixed']['final-eol']['file_9.txt'] = '';

// --final-eol=2
$structure['initial']['final-eol-2']['file_1.txt'] = "\r\r\rlorem ipsum";
$structure['fixed']['final-eol-2']['file_1.txt'] = "\r\r\rlorem ipsum\r";

$structure['initial']['final-eol-2']['file_2.txt'] = "\n\n\nlorem ipsum";
$structure['fixed']['final-eol-2']['file_2.txt'] = "\n\n\nlorem ipsum\n";

$structure['initial']['final-eol-2']['file_3.txt'] = "\r\n\r\nlorem ipsum";
$structure['fixed']['final-eol-2']['file_3.txt'] = "\r\n\r\nlorem ipsum\r\n";

$structure['initial']['final-eol-2']['file_4.txt'] = "\n\nlorem ipsum\n";
$structure['fixed']['final-eol-2']['file_4.txt'] = "\n\nlorem ipsum\n";

$structure['initial']['final-eol-2']['file_5.txt'] = "\n\nlorem ipsum\n\n";
$structure['fixed']['final-eol-2']['file_5.txt'] = "\n\nlorem ipsum\n\n";

$structure['initial']['final-eol-2']['file_6.txt'] = "\n\nlorem ipsum\n\n\n";
$structure['fixed']['final-eol-2']['file_6.txt'] = "\n\nlorem ipsum\n\n";

$structure['initial']['final-eol-2']['file_7.txt'] = "\n\n\n\n\n";
$structure['fixed']['final-eol-2']['file_7.txt'] = "\n\n";

$structure['initial']['final-eol-2']['file_8.txt'] = "\n\n  \n\n\n";
$structure['fixed']['final-eol-2']['file_8.txt'] = "\n\n  \n\n";

$structure['initial']['final-eol-2']['file_9.txt'] = "\r\n\r\n  \r\n\r\n\r\n";
$structure['fixed']['final-eol-2']['file_9.txt'] = "\r\n\r\n  \r\n\r\n";

$structure['initial']['final-eol-2']['file_10.txt'] = 'lorem ipsum dolor sit amet';
$structure['fixed']['final-eol-2']['file_10.txt'] = "lorem ipsum dolor sit amet\n";

// CRLF files.
$structure['initial']['final-eol-crlf']['file_1.txt'] = "lorem\r\nipsum\r\ndolor\r\nsit\r\namet";
$structure['fixed']['final-eol-crlf']['file_1.txt'] = "lorem\r\nipsum\r\ndolor\r\nsit\r\namet\r\n";

$structure['initial']['final-eol-crlf']['file_2.txt'] = 'lorem ipsum dolor sit amet';
$structure['fixed']['final-eol-crlf']['file_2.txt'] = "lorem ipsum dolor sit amet\r\n";

return $structure;
