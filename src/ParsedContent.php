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
        public readonly string $short_description,
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
        $others = array_filter(
            $data,
            fn($key) => ! in_array($key, ['name', 'short_description', 'sections'], true),
            ARRAY_FILTER_USE_KEY,
        );

        return new self(
            $data['name'] ?? '',
            $data['short_description'] ?? '',
            $data['sections'] ?? [],
            ...$others,
        );
    }
}
