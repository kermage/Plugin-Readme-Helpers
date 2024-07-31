<?php

/**
 * @package Plugin Readme Helpers
 */

namespace kermage\PluginReadmeHelpers;

class Parser
{
    public const HEADERS_MAP = [
        'tested up to' => 'tested',
        'requires at least' => 'requires',
        'requires php' => 'requires_php',
        'stable tag' => 'stable_tag',
        'contributors' => 'contributors',
        'donate link' => 'donate_link',
        'license uri' => 'license_uri',
        'license' => 'license',
        'tags' => 'tags',
    ];

    public const HEADER_TRIMMER = '#= \t';

    public function parse(string $content): array
    {
        $lines = explode("\n", $content);
        $data = ['name' => trim($this->getNextNonEmptyLine($lines), self::HEADER_TRIMMER)];
        $headers = [];

        while (null !== $line = array_shift($lines)) {
            if (empty(trim($line))) {
                if (empty($headers)) {
                    continue;
                }

                break;
            }

            $header = $this->maybeHeader($line);

            if ($header) {
                $headers[self::HEADERS_MAP[$header['key']]] = $header['value'];
            }
        }

        $data += $headers;

        $data['short_description'] = trim($this->getNextNonEmptyLine($lines));

        return $data;
    }

    protected function getNextNonEmptyLine(&$lines): string
    {
        while (null !== $line = array_shift($lines)) {
            if (!empty(trim($line))) {
                break;
            }
        }

        return $line ?? '';
    }

    protected function maybeHeader(string $line): ?array
    {
        if (! str_contains($line, ':') || str_starts_with($line, '#') || str_starts_with($line, '=')) {
            return null;
        }

        list($key, $value) = explode(':', $line, 2);
        $key = strtolower(trim($key));
        $value = trim($value);

        if (! in_array($key, array_keys(self::HEADERS_MAP))) {
            return null;
        }

        return compact('key', 'value');
    }
}
