<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

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

    protected function __construct()
    {
    }

    public static function parse(string $data): ParsedContent
    {
        $data = trim($data);

        if (false === strpos($data, "\n")) {
            $data = (file_exists($data) || filter_var($data, FILTER_VALIDATE_URL))
                ? (string) file_get_contents($data)
                : '';
        }

        return (new self())->parseString($data);
    }

    protected function parseString(string $content): ParsedContent
    {
        $content = str_replace("\r", "", $content);
        $lines = explode("\n", $content);
        $data = ['name' => trim($this->getNextNonEmptyLine($lines), self::HEADER_TRIMMER)];
        $isPhp = str_contains($data['name'], '<?php');
        $mapper = self::HEADERS_MAP;
        $forPhp = [
            'plugin uri' => 'Plugin URI',
            'author' => 'Author',
            'author uri' => 'Author URI',
            'text domain' => 'Text Domain',
            'domain path' => 'Domain Path',
            'network' => 'Network',
            'update uri' => 'Update URI',
            'requires plugins' => 'Requires Plugins',
        ];

        if ($isPhp) {
            while ('' !== $data['name'] && ! str_starts_with($data['name'], '*')) {
                $data['name'] = trim($this->getNextNonEmptyLine($lines));
            }

            if ('' !== $data['name']) {
                array_unshift($lines, $data['name']);
                unset($data['name']);
            }

            $mapper += [
                'plugin name' => 'name',
                'version' => 'stable_tag',
                'description' => 'short_description',
            ];
            $mapper += $forPhp;
        }

        $data += $this->getHeaders($lines, $mapper);

        if (! $isPhp) {
            $data['short_description'] = trim($this->getNextNonEmptyLine($lines));
        }

        $data['sections'] = $this->getAndSetSections($lines);

        if ($isPhp) {
            $metadata = [];

            foreach ($forPhp as $key) {
                if (empty($data[$key])) {
                    continue;
                }

                $metadata[$key] = $data[$key];
                unset($data[$key]);
            }

            if ([] !== $metadata) {
                $data['sections']['other_notes'] = json_encode($metadata);
            }
        }

        return ParsedContent::create($data);
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

    protected function maybePhpOrComment(string $line): bool
    {
        return str_contains($line, '<?php') || str_contains($line, '/**');
    }

    /**
     * @param string[] $lines
     * @param array<string, string> $mapper
     * @return array<string, string>
     */
    protected function getHeaders(array &$lines, array $mapper): array
    {
        $headers = [];

        while (null !== $line = array_shift($lines)) {
            if ('' === trim($line)) {
                if ([] === $headers) {
                    continue;
                }

                break;
            }

            $header = $this->maybeHeader($line, $mapper);

            if (null !== $header) {
                $headers[$header['key']] = $header['value'];
            }
        }

        return $headers;
    }

    /**
     * @param array<string, string> $mapper
     * @return array{key: string, value: string}|null
     */
    protected function maybeHeader(string $line, array $mapper): ?array
    {
        if (! str_contains($line, ':') || str_starts_with($line, '#') || str_starts_with($line, '=')) {
            return null;
        }

        [$key, $value] = explode(':', $line, 2);
        $key = strtolower(trim($key, "* \t"));
        $value = trim($value);
        $map = self::HEADERS_MAP;

        if (! in_array($key, array_keys($mapper))) {
            return null;
        }

        $key = $mapper[$key];

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
