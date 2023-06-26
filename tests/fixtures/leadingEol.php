<?php

/**
 * Leading EOL fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'leading-eol' => [],
    ],
    'fixed' => [
        'leading-eol' => [],
    ],
];

$structure['initial']['leading-eol']['file_1.txt'] = "\nlorem ipsum";
$structure['fixed']['leading-eol']['file_1.txt'] = 'lorem ipsum';

$structure['initial']['leading-eol']['file_2.txt'] = "\n\n\nlorem ipsum";
$structure['fixed']['leading-eol']['file_2.txt'] = 'lorem ipsum';

$structure['initial']['leading-eol']['file_3.txt'] = "\r\n\r\nlorem ipsum";
$structure['fixed']['leading-eol']['file_3.txt'] = 'lorem ipsum';

$structure['initial']['leading-eol']['file_4.txt'] = "\r\n\nlorem ipsum";
$structure['fixed']['leading-eol']['file_4.txt'] = 'lorem ipsum';

$structure['initial']['leading-eol']['file_5.txt'] = "\r\r\rlorem ipsum";
$structure['fixed']['leading-eol']['file_5.txt'] = "lorem ipsum";

$structure['initial']['leading-eol']['file_6.txt'] = "\n\n\n\n";
$structure['fixed']['leading-eol']['file_6.txt'] = '';

return $structure;
