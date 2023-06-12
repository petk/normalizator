#!/usr/bin/env php
<?php

/**
 * Helper script that creates test fixtures files with different encodings.
 */

declare(strict_types=1);

use function Normalizator\file_get_contents;
use function Normalizator\file_put_contents;
use function Normalizator\mb_convert_encoding;

require __DIR__ . '/../vendor/autoload.php';

// Create file with ISO-8859-2 encoding.
$string = "čČćĆšŠđĐžŽčČćĆšŠđĐžŽčČćĆšŠđĐžŽ Ă řŘ ňŇ\n";
$encoded = mb_convert_encoding($string, 'ISO-8859-2', 'UTF-8');
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/encoding/iso-8859-2.txt', $encoded);

// Create file with Windows-1252 encoding.
$string = "äÄëËïÏöÖüÜ šŠžŽ\n";
$encoded = mb_convert_encoding($string, 'Windows-1252', 'UTF-8');
if (!is_array($encoded)) {
    file_put_contents(__DIR__ . '/../../tests/fixtures/initial/encoding/windows-1252.txt', $encoded);
}

// Create fixed file that converts the ISO-8859-2 encoding to UTF-8.
$string = file_get_contents(__DIR__ . '/../../tests/fixtures/initial/encoding/iso-8859-2.txt');
$encoded = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-2');
if (!is_array($encoded)) {
    file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/encoding/iso-8859-2.txt', $encoded);
}

// Create fixed file that converts the Windows-1252 encoding to UTF-8.
$string = file_get_contents(__DIR__ . '/../../tests/fixtures/initial/encoding/windows-1252.txt');
$encoded = mb_convert_encoding($string, 'UTF-8', 'Windows-1252');
if (!is_array($encoded)) {
    file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/encoding/windows-1252.txt', $encoded);
}
