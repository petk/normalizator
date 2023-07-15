<?php

declare(strict_types=1);

namespace Normalizator\Util;

use Exception;

use function json_decode;
use function Normalizator\file_get_contents;
use function stream_context_create;

use const JSON_THROW_ON_ERROR;

/**
 * Client for fetching the latest release from the GitHub API.
 */
class ApiClient
{
    private string $url = 'https://api.github.com/repos/petk/normalizator/releases/latest';

    /**
     * Get latest release from GitHub API.
     */
    public function fetch(): null|string
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'header' => 'User-Agent: petk/normalizator',
                ],
            ]);

            $json = file_get_contents($this->url, false, $context);

            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception $e) {
            return null;
        }

        return $data['tag_name'] ?? null;
    }
}
