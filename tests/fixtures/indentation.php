<?php

/**
 * Indentation fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'indentation' => [],
        'indentation-2' => [],
    ],
    'fixed' => [
        'indentation' => [],
        'indentation-2' => [],
    ]
];

// --indentation=space --indentation-size=4
$structure['initial']['indentation']['file_1.txt'] = "    README\n\tLorem ipsum dolor sit amet.\n\n    \tLorem ipsum dolor sit amet,\n\nconsectetur adipiscing elit\n\t\t    \t    \t    sed do eiusmod tempor\n                        incididunt ut\n\t\t\t\t\t\tlabore et dolore magna aliqua.\n";
$structure['fixed']['indentation']['file_1.txt'] = "    README\n    Lorem ipsum dolor sit amet.\n\n        Lorem ipsum dolor sit amet,\n\nconsectetur adipiscing elit\n                            sed do eiusmod tempor\n                        incididunt ut\n                        labore et dolore magna aliqua.\n";

$structure['initial']['indentation']['file_2.txt'] = "# README\n\t## About\n\n\t\tLorem ipsum dolor sit amet,\n\n\t\t\tconsectetur adipiscing elit,\n\n\t\tsed do eiusmod tempor incididunt\n\t";
$structure['fixed']['indentation']['file_2.txt'] = "# README\n    ## About\n\n        Lorem ipsum dolor sit amet,\n\n            consectetur adipiscing elit,\n\n        sed do eiusmod tempor incididunt\n    ";

$structure['initial']['indentation']['file_3.txt'] = "README\n";
$structure['fixed']['indentation']['file_3.txt'] = "README\n";

$structure['initial']['indentation']['file_4.txt'] = '';
$structure['fixed']['indentation']['file_4.txt'] = '';

$structure['initial']['indentation']['file_5.txt'] = "    README\n\n    Lorem ipsum dolor sit amet.\n";
$structure['fixed']['indentation']['file_5.txt'] = "    README\n\n    Lorem ipsum dolor sit amet.\n";

$structure['initial']['indentation']['file_6.txt'] = "\tREADME";
$structure['fixed']['indentation']['file_6.txt'] = '    README';

$structure['initial']['indentation']['file_7.txt'] = "\tREADME\r\n\t\tLorem ipsum\r\n\r\n\t\t    \t    \tdolor sit\r\n\t";
$structure['fixed']['indentation']['file_7.txt'] = "    README\r\n        Lorem ipsum\r\n\r\n                        dolor sit\r\n    ";

$structure['initial']['indentation']['file_8.txt'] = "\tREADME\r\t\tLorem ipsum\r\r\t\t    \t    \tdolor sit\r\t";
$structure['fixed']['indentation']['file_8.txt'] = "    README\r        Lorem ipsum\r\r                        dolor sit\r    ";

// --indentation=space --indentation-size=2
$structure['initial']['indentation-2']['file_1.txt'] = "\tREADME";
$structure['fixed']['indentation-2']['file_1.txt'] = '  README';

$structure['initial']['indentation-2']['file_2.txt'] = "\tREADME\r\n\t\tLorem ipsum\r\n\r\n\t\t    \t    \tdolor sit\r\n\t";
$structure['fixed']['indentation-2']['file_2.txt'] = "  README\r\n    Lorem ipsum\r\n\r\n                dolor sit\r\n  ";

$structure['initial']['indentation-2']['file_3.txt'] = "\tREADME\r\t\tLorem ipsum\r\r\t\t    \t    \tdolor sit\r\t";
$structure['fixed']['indentation-2']['file_3.txt'] = "  README\r    Lorem ipsum\r\r                dolor sit\r  ";

return $structure;
