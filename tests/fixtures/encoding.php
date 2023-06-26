<?php

/**
 * Encoding fixture files for vfsStream.
 */

declare(strict_types=1);

use function Normalizator\mb_convert_encoding;

$structure = [
    'initial' => [
        'encoding' => [],
    ],
    'fixed' => [
        'encoding' => [],
    ],
];

// Create file with ISO-8859-2 encoded content.
$content = "čČćĆšŠđĐžŽčČćĆšŠđĐžŽčČćĆšŠđĐžŽ Ă řŘ ňŇ\n";
$encoded = mb_convert_encoding($content, 'ISO-8859-2', 'UTF-8');
$structure['initial']['encoding']['iso-8859-2.txt'] = $encoded;
$structure['fixed']['encoding']['iso-8859-2.txt'] = $content;

// Create file with Windows-1252 encoded content.
$content = "äÄëËïÏöÖüÜ šŠžŽ\n";
$encoded = mb_convert_encoding($content, 'Windows-1252', 'UTF-8');
$structure['initial']['encoding']['windows-1252.txt'] = $encoded;
$structure['fixed']['encoding']['windows-1252.txt'] = $content;

// Create file with UTF-8 encoded content.
$content = "čČšŠćĆđĐžŽ\n";
$structure['initial']['encoding']['utf-8.txt'] = $content;
$structure['fixed']['encoding']['utf-8.txt'] = $content;

return $structure;
