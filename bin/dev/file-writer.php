#!/usr/bin/env php
<?php

/**
 * Helper script that creates tests fixtures files with different EOL styles.
 */

declare(strict_types=1);

use function Normalizator\file_put_contents;

require __DIR__ . '/../vendor/autoload.php';

// Final EOL
$content = "\r\r\rlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/final-eol/file-8.txt', $content);

$content = "\r\r\rlorem ipsum\r";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/final-eol/file-8.txt', $content);

// leading EOL
$content = "\nlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/leading-eol/file-1.txt', $content);
$content = 'lorem ipsum';
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/leading-eol/file-1.txt', $content);

$content = "\n\n\nlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/leading-eol/file-2.txt', $content);
$content = 'lorem ipsum';
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/leading-eol/file-2.txt', $content);

$content = "\r\n\r\nlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/leading-eol/file-3.txt', $content);
$content = 'lorem ipsum';
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/leading-eol/file-3.txt', $content);

$content = "\r\n\nlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/leading-eol/file-4.txt', $content);
$content = 'lorem ipsum';
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/leading-eol/file-4.txt', $content);

$content = "\r\r\rlorem ipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/leading-eol/file-5.txt', $content);
$content = 'lorem ipsum';
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/leading-eol/file-5.txt', $content);

// Middle EOL
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-1.txt', $content);
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-1.txt', $content);

$content = "\nlorem \nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-2.txt', $content);
$content = "\nlorem \nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-2.txt', $content);

$content = "\nlorem \n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-3.txt', $content);
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-3.txt', $content);

$content = "\nlorem \n\n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-4.txt', $content);
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-4.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-5.txt', $content);
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-5.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-6.txt', $content);
$content = "\nlorem \n\nipsum\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-6.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum\ndolor sit \n\namet\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-7.txt', $content);
$content = "\nlorem \n\nipsum\ndolor sit \n\namet\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-7.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-8.txt', $content);
$content = "\nlorem \n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-8.txt', $content);

$content = "\n\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-9.txt', $content);
$content = "\n\nlorem \n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-9.txt', $content);

$content = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol/file-10.txt', $content);
$content = "\r\nlorem \r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol/file-10.txt', $content);

// Middle EOL max=2
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-1.txt', $content);
$content = "\nlorem \n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-1.txt', $content);

$content = "\nlorem \n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-2.txt', $content);
$content = "\nlorem \n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-2.txt', $content);

$content = "\nlorem \n\n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-3.txt', $content);
$content = "\nlorem \n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-3.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-4.txt', $content);
$content = "\nlorem \n\n\nipsum";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-4.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-5.txt', $content);
$content = "\nlorem \n\n\nipsum\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-5.txt', $content);

$content = "\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-6.txt', $content);
$content = "\nlorem \n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-6.txt', $content);

$content = "\n\nlorem \n\n\n\n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-7.txt', $content);
$content = "\n\nlorem \n\n\nipsum\n\n\n\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-7.txt', $content);

$content = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-8.txt', $content);
$content = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-8.txt', $content);

$content = "\r\nlorem \r\n\r\n\r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/initial/middle-eol-2/file-9.txt', $content);
$content = "\r\nlorem \r\n\r\n\r\nipsum\r\n\r\n";
file_put_contents(__DIR__ . '/../../tests/fixtures/fixed/middle-eol-2/file-9.txt', $content);
