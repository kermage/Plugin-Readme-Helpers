<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

readonly class ParsedContent
{
    use ParsesCSV;

    public function __construct(
        public string $name,
        public string $stable_tag,
        public string $short_description,
        public string $requires,
        public string $requires_php,
        public string $tested,
        /** @var string[] */
        public array $contributors,
        public string $donate_link,
        public string $license_uri,
        public string $license,
        /** @var string[] */
        public array $tags,
        /** @var array<string, string> */
        public array $sections,
        public Metadata $metadata,
    ) {
    }

    /** @param array<mixed> $data */
    public static function create(array $data): ParsedContent
    {
        return new self(
            $data['name'] ?? '',
            $data['stable_tag'] ?? '',
            $data['short_description'] ?? '',
            $data['requires'] ?? '',
            $data['requires_php'] ?? '',
            $data['tested'] ?? '',
            self::parseCommaSeparated($data['contributors'] ?? ''),
            $data['donate_link'] ?? '',
            $data['license_uri'] ?? '',
            $data['license'] ?? '',
            self::parseCommaSeparated($data['tags'] ?? ''),
            $data['sections'] ?? [],
            Metadata::create($data['metadata'] ?? []),
        );
    }
}
