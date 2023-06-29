<?php

/**
 * Middle EOL fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'middle-eol' => [],
        'middle-eol-2' => [],
    ],
    'fixed' => [
        'middle-eol' => [],
        'middle-eol-2' => [],
    ],
];

// --middle-eol=1
$structure['initial']['middle-eol']['file_1.txt'] = "\nlorem \n\nipsum";
$structure['fixed']['middle-eol']['file_1.txt'] = "\nlorem \n\nipsum";

$structure['initial']['middle-eol']['file_2.txt'] = "\nlorem \nipsum";
$structure['fixed']['middle-eol']['file_2.txt'] = "\nlorem \nipsum";

$structure['initial']['middle-eol']['file_3.txt'] = "\nlorem \n\n\nipsum";
$structure['fixed']['middle-eol']['file_3.txt'] = "\nlorem \n\nipsum";

$structure['initial']['middle-eol']['file_4.txt'] = "\nlorem \n\n\n\nipsum";
$structure['fixed']['middle-eol']['file_4.txt'] = "\nlorem \n\nipsum";

$structure['initial']['middle-eol']['file_5.txt'] = "\nlorem \n\n\n\n\n\nipsum";
$structure['fixed']['middle-eol']['file_5.txt'] = "\nlorem \n\nipsum";

$structure['initial']['middle-eol']['file_6.txt'] = "\nlorem \n\n\n\n\n\nipsum\n";
$structure['fixed']['middle-eol']['file_6.txt'] = "\nlorem \n\nipsum\n";

$structure['initial']['middle-eol']['file_7.txt'] = "\nlorem \n\n\n\n\n\nipsum\ndolor sit \n\namet\n";
$structure['fixed']['middle-eol']['file_7.txt'] = "\nlorem \n\nipsum\ndolor sit \n\namet\n";

$structure['initial']['middle-eol']['file_8.txt'] = "\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
$structure['fixed']['middle-eol']['file_8.txt'] = "\nlorem \n\nipsum\n\n\n\n";

$structure['initial']['middle-eol']['file_9.txt'] = "\n\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
$structure['fixed']['middle-eol']['file_9.txt'] = "\n\nlorem \n\nipsum\n\n\n\n";

$structure['initial']['middle-eol']['file_10.txt'] = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
$structure['fixed']['middle-eol']['file_10.txt'] = "\r\nlorem \r\n\r\nipsum\r\n\r\n";

$structure['initial']['middle-eol']['file_11.txt'] = "\r\nlorem \r\n\r\n\r\n\r\nipsum\r\n\r\ndolor \r\n\r\n\r\nsit\r\n\r\n\r\namet\r\n\r\n\r\n";
$structure['fixed']['middle-eol']['file_11.txt'] = "\r\nlorem \r\n\r\nipsum\r\n\r\ndolor \r\n\r\nsit\r\n\r\namet\r\n\r\n\r\n";

$structure['initial']['middle-eol']['file_12.txt'] = "  \n  \n  \nlorem\n  \n  \n  \n\n\n\n  \n  \nipsum dolor sit\n\n\n\n\namet\n\n\n";
$structure['fixed']['middle-eol']['file_12.txt'] = "  \n  \n  \nlorem\n  \n  \n  \n\n  \n  \nipsum dolor sit\n\namet\n\n\n";

// --middle-eol=2
$structure['initial']['middle-eol-2']['file_1.txt'] = "\nlorem \n\nipsum";
$structure['fixed']['middle-eol-2']['file_1.txt'] = "\nlorem \n\nipsum";

$structure['initial']['middle-eol-2']['file_2.txt'] = "\nlorem \n\n\nipsum";
$structure['fixed']['middle-eol-2']['file_2.txt'] = "\nlorem \n\n\nipsum";

$structure['initial']['middle-eol-2']['file_3.txt'] = "\nlorem \n\n\n\nipsum";
$structure['fixed']['middle-eol-2']['file_3.txt'] = "\nlorem \n\n\nipsum";

$structure['initial']['middle-eol-2']['file_4.txt'] = "\nlorem \n\n\n\n\n\nipsum";
$structure['fixed']['middle-eol-2']['file_4.txt'] = "\nlorem \n\n\nipsum";

$structure['initial']['middle-eol-2']['file_5.txt'] = "\nlorem \n\n\n\n\n\nipsum\n";
$structure['fixed']['middle-eol-2']['file_5.txt'] = "\nlorem \n\n\nipsum\n";

$structure['initial']['middle-eol-2']['file_6.txt'] = "\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
$structure['fixed']['middle-eol-2']['file_6.txt'] = "\nlorem \n\n\nipsum\n\n\n\n";

$structure['initial']['middle-eol-2']['file_7.txt'] = "\n\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
$structure['fixed']['middle-eol-2']['file_7.txt'] = "\n\nlorem \n\n\nipsum\n\n\n\n";

$structure['initial']['middle-eol-2']['file_8.txt'] = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
$structure['fixed']['middle-eol-2']['file_8.txt'] = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";

$structure['initial']['middle-eol-2']['file_9.txt'] = "\r\nlorem \r\n\r\n\r\n\r\nipsum\r\n\r\n";
$structure['fixed']['middle-eol-2']['file_9.txt'] = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";

return $structure;
