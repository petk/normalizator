<?php

/**
 * Extensions fixture files for vfsStream.
 */

declare(strict_types=1);

$structure = [
    'initial' => [
        'extension' => [],
        'extension-duplicates' => [],
        'extension-duplicates-after-rename' => [],
    ],
    'fixed' => [
        'extension' => [],
        'extension-duplicates' => [],
        'extension-duplicates-after-rename' => [],
    ],
];

$structure['initial']['extension']['.DS_Store'] = '';
$structure['fixed']['extension']['.DS_Store'] = '';

$structure['initial']['extension']['~fooBar.JPEG'] = '';
$structure['fixed']['extension']['~fooBar.jpg'] = '';

$structure['initial']['extension']['extension-has-space.txt '] = '';
$structure['fixed']['extension']['extension-has-space.txt '] = '';

$structure['initial']['extension']['foobar.jpeg'] = '';
$structure['fixed']['extension']['foobar.jpg'] = '';

$structure['initial']['extension']['foobar.jpeg-'] = '';
$structure['fixed']['extension']['foobar.jpg'] = '';

$structure['initial']['extension']['fooBar.JPG'] = '';
$structure['fixed']['extension']['fooBar.jpg'] = '';

$structure['initial']['extension']['foobar.xml'] = '';
$structure['fixed']['extension']['foobar.xml'] = '';

$structure['initial']['extension']['noextension'] = '';
$structure['fixed']['extension']['noextension'] = '';

$structure['initial']['extension']['this.is.some-script'] = '';
$structure['fixed']['extension']['this.is.some-script'] = '';

// Files with duplicates after rename, so file checker needs to create suffixed name.
$structure['initial']['extension-duplicates']['foo bar.txt'] = '';
$structure['fixed']['extension-duplicates'][''] = '';

$structure['initial']['extension-duplicates']['foo bar.TXT'] = '';
$structure['fixed']['extension-duplicates'][''] = '';

$structure['initial']['extension-duplicates']['foo-bar_1.txt'] = '';
$structure['fixed']['extension-duplicates'][''] = '';

$structure['initial']['extension-duplicates']['foo-bar.txt'] = '';
$structure['fixed']['extension-duplicates'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo bar.txt'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo bar.TXT'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo--bar.TXT'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo-bar_1.txt'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo-bar.txt'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo-bar.TXt'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

$structure['initial']['extension-duplicates-after-rename']['foo-bar.TXT'] = '';
$structure['fixed']['extension-duplicates-after-rename'][''] = '';

return $structure;
