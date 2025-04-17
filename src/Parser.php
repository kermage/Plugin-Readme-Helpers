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

    protected bool $isPhp;

    protected function __construct()
    {
    }

    /** @return ParsedContent */
    public static function parse(string $data): array
    {
        $data = trim($data);

        if (false === strpos($data, "\n")) {
            $data = (file_exists($data) || filter_var($data, FILTER_VALIDATE_URL))
                ? (string) file_get_contents($data)
                : '';
        }

        return (new self())->parseString($data);
    }

    /** @return ParsedContent */
    protected function parseString(string $content): array
    {
        $content = str_replace("\r", "", $content);
        $lines = explode("\n", $content);
        $data = ['name' => trim($this->getNextNonEmptyLine($lines), self::HEADER_TRIMMER)];
        $this->isPhp = $this->maybePhpOrComment($data['name']);

        if ($this->isPhp) {
            while ($this->maybePhpOrComment($data['name'])) {
                $data['name'] = trim($this->getNextNonEmptyLine($lines));
            }

            if (str_starts_with($data['name'], '*')) {
                array_unshift($lines, $data['name']);
                unset($data['name']);
            }
        }

        $data += $this->getHeaders($lines);

        if (! $this->isPhp) {
            $data['short_description'] = trim($this->getNextNonEmptyLine($lines));
        }

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

    protected function maybePhpOrComment(string $line): bool
    {
        return str_contains($line, '<?php') || str_contains($line, '/**');
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
        $key = strtolower(trim($key, "* \t"));
        $value = trim($value);
        $map = self::HEADERS_MAP;

        if ($this->isPhp) {
            $map += [
                'plugin name' => 'name',
                'version' => 'stable_tag',
                'description' => 'short_description',
            ];
        }

        if (! in_array($key, array_keys($map))) {
            return null;
        }

        $key = $map[$key];

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
