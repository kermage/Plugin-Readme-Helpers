<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

/**
 * @phpstan-type ParsedContent array{
 *     name: string,
 *     short_description: string,
 *     sections: array<string, string>,
 *     ...<string, string>,
 * }
 */
class Parser
{
    public const HEADERS_MAP = [
        'tested' => 'tested',
        'tested up to' => 'tested',
        'requires' => 'requires',
        'requires at least' => 'requires',
        'requires php' => 'requires_php',
        'stable tag' => 'stable_tag',
        'contributors' => 'contributors',
        'donate link' => 'donate_link',
        'license uri' => 'license_uri',
        'license' => 'license',
        'tags' => 'tags',
    ];

    public const SECTIONS_MAP = [
        'frequently_asked_questions' => 'faq',
        'change_log' => 'changelog',
        'screenshot' => 'screenshots',
    ];

    public const HEADER_TRIMMER = "#= \t";

    /** @return ParsedContent */
    public function parse(string $content): array
    {
        $lines = explode("\n", $content);
        $data = ['name' => trim($this->getNextNonEmptyLine($lines), self::HEADER_TRIMMER)];
        $data += $this->getHeaders($lines);

        $data['short_description'] = trim($this->getNextNonEmptyLine($lines));
        $data['sections'] = $this->getAndSetSections($lines);

        /** @var ParsedContent $data */
        return $data;
    }

    /** @param string[] $lines */
    protected function getNextNonEmptyLine(array &$lines): string
    {
        while (null !== $line = array_shift($lines)) {
            if ('' !== trim($line)) {
                break;
            }
        }

        return $line ?? '';
    }

    /**
     * @param string[] $lines
     * @return array<string, string>
     */
    protected function getHeaders(array &$lines): array
    {
        $headers = [];

        while (null !== $line = array_shift($lines)) {
            if ('' === trim($line)) {
                if ([] === $headers) {
                    continue;
                }

                break;
            }

            $header = $this->maybeHeader($line);

            if (null !== $header) {
                $headers[$header['key']] = $header['value'];
            }
        }

        return $headers;
    }

    /** @return array{key: string, value: string}|null */
    protected function maybeHeader(string $line): ?array
    {
        if (! str_contains($line, ':') || str_starts_with($line, '#') || str_starts_with($line, '=')) {
            return null;
        }

        [$key, $value] = explode(':', $line, 2);
        $key = strtolower(trim($key));
        $value = trim($value);

        if (! in_array($key, array_keys(self::HEADERS_MAP))) {
            return null;
        }

        $key = self::HEADERS_MAP[$key];

        return compact('key', 'value');
    }

    /**
     * @param string[] $lines
     * @return array<string, string>
     */
    protected function getAndSetSections(array &$lines): array
    {
        $sections = [];
        $title = '';
        $content = '';

        while (null !== $line = array_shift($lines)) {
            $line = trim($line);

            if (
                (str_starts_with($line, '##') && '#' !== $line[2]) ||
                (str_starts_with($line, '==') && '=' !== $line[2])
            ) {
                if ('' !== $title) {
                    $sections[$title] = trim($content);
                }

                $content = '';
                $title = strtolower(str_replace(' ', '_', trim($line, self::HEADER_TRIMMER)));

                if (isset(self::SECTIONS_MAP[$title])) {
                    $title = self::SECTIONS_MAP[$title];
                }
            }

            $content .= $line . "\n";
        }

        if ('' !== $title) {
            $sections[$title] = trim($content);
        }

        return $sections;
    }
}
