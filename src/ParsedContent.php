<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

class ParsedContent
{
    public function __construct(
        public readonly string $name,
        public readonly string $stable_tag,
        public readonly string $short_description,
        public readonly string $requires,
        public readonly string $requires_php,
        public readonly string $tested,
        public readonly string $contributors,
        public readonly string $donate_link,
        public readonly string $license_uri,
        public readonly string $license,
        public readonly string $tags,
        /** @var array<string, string> */
        public readonly array $sections,
        /** @var array<string, string> */
        public readonly array $metadata,
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
            $data['contributors'] ?? '',
            $data['donate_link'] ?? '',
            $data['license_uri'] ?? '',
            $data['license'] ?? '',
            $data['tags'] ?? '',
            $data['sections'] ?? [],
            $data['metadata'] ?? [],
        );
    }
}
