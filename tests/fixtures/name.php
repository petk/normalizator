<?php

/**
 * Path name fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'name' => [
            'files-with-duplicates-after-rename' => [],
        ],
    ],
    'fixed' => [
        'name' => [],
    ],
];

$structure['initial']['name']['-foobar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['~fooBar.xls'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['äÄëËïÏöÖüÜ.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['čČćĆšŠđĐžŽ.doc'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo       -    bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo  -     -    bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo - bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo -bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo bar.xml'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo- bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo--bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo,bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo;Bar.doc'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foo#bar.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['foobar.txt-'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['fooBar.xls'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['lorem ipsum   &   dolor sit amet.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['lorem ipsum & dolor sit amet.txt'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['noextension'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['update.sample'] = '';
$structure['fixed']['name'][''] = '';

$structure['initial']['name']['update.something.sample'] = '';
$structure['fixed']['name'][''] = '';

// Files with duplicates after renaming.

$structure['initial']['name']['files-with-duplicates-after-rename']['foo bar.txt'] = "foo bar.txt\n";
$structure['fixed']['name']['files-with-duplicates-after-rename'][''] = '';

$structure['initial']['name']['files-with-duplicates-after-rename']['foo--bar.txt'] = "foo--bar.txt\n";
$structure['fixed']['name']['files-with-duplicates-after-rename'][''] = '';

$structure['initial']['name']['files-with-duplicates-after-rename']['foo-bar_1.txt'] = "foo-bar_1.txt\n";
$structure['fixed']['name']['files-with-duplicates-after-rename'][''] = '';

$structure['initial']['name']['files-with-duplicates-after-rename']['foo-bar.txt'] = "foo-bar.txt\n";
$structure['fixed']['name']['files-with-duplicates-after-rename'][''] = '';

return $structure;
