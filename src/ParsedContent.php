<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

use AllowDynamicProperties;

#[AllowDynamicProperties]
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
        string ...$others,
    ) {
        foreach ($others as $key => $value) {
            $this->$key = $value;
        }
    }

    /** @param array<mixed> $data */
    public static function create(array $data): ParsedContent
    {
        $known = [
            'name',
            'stable_tag',
            'short_description',
            'sections',
            'requires',
            'requires_php',
            'tested',
            'contributors',
            'donate_link',
            'license_uri',
            'license',
            'tags',
        ];
        $others = array_filter(
            $data,
            fn($key) => ! in_array($key, $known, true),
            ARRAY_FILTER_USE_KEY,
        );

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
            ...$others,
        );
    }
}
