<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace kermage\PluginReadmeHelpers;

readonly class Metadata
{
    public function __construct(
        public string $Name,
        public string $PluginURI,
        public string $Version,
        public string $Description,
        public string $Author,
        public string $AuthorURI,
        public string $TextDomain,
        public string $DomainPath,
        public string $Network,
        public string $RequiresWP,
        public string $RequiresPHP,
        public string $UpdateURI,
        public string $RequiresPlugins,
        public string $Title,
        public string $AuthorName,
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
