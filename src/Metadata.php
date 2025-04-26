<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

class Metadata
{
    public function __construct(
        public readonly string $Name,
        public readonly string $PluginURI,
        public readonly string $Version,
        public readonly string $Description,
        public readonly string $Author,
        public readonly string $AuthorURI,
        public readonly string $TextDomain,
        public readonly string $DomainPath,
        public readonly string $Network,
        public readonly string $RequiresWP,
        public readonly string $RequiresPHP,
        public readonly string $UpdateURI,
        public readonly string $RequiresPlugins,
        public readonly string $Title,
        public readonly string $AuthorName,
    ) {
    }

    /** @param array<mixed> $data */
    public static function create(array $data): Metadata
    {
        $data['Plugin Name'] ??= $data['Title'] ?? '';
        $data['Author Name'] ??= $data['Author'] ?? '';
        $data['Title'] ??= $data['Plugin Name'];
        $data['Author'] ??= $data['Author Name'];

        return new self(
            $data['Plugin Name'],
            $data['Plugin URI'] ?? '',
            $data['Version'] ?? '',
            $data['Description'] ?? '',
            $data['Author'],
            $data['Author URI'] ?? '',
            $data['Text Domain'] ?? '',
            $data['Domain Path'] ?? '',
            $data['Network'] ?? '',
            $data['Requires at least'] ?? '',
            $data['Requires PHP'] ?? '',
            $data['Update URI'] ?? '',
            $data['Requires Plugins'] ?? '',
            $data['Title'],
            $data['Author Name'],
        );
    }
}
