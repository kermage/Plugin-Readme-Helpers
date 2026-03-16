<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

trait ParsesCSV
{
    /** @return string[] */
    protected static function parseCommaSeparated(string $value): array
    {
        if ('' === $value) {
            return [];
        }

        return array_map('trim', explode(',', $value));
    }
}
