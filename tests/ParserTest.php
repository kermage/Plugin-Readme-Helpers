<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use kermage\PluginReadmeHelpers\Parser;
use PHPUnit\Framework\Attributes\DataProvider;

/** @phpstan-import-type ParsedContent from Parser */
final class ParserTest extends TestCase
{
    /** @return array<int, string[]> */
    public static function forTestParse(): array
    {
        return [
            [ __DIR__ . '/fixtures/readme.txt' ],
            [ __DIR__ . '/fixtures/readme.md' ],
            [ 'https://raw.githubusercontent.com/kermage/Plugin-Readme-Helpers/refs/heads/main/tests/fixtures/readme.txt' ], // phpcs:ignore Generic.Files.LineLength.TooLong
        ];
    }

    #[DataProvider('forTestParse')]
    public function testParse(string $file): void
    {
        $parsed = Parser::parse($file);

        $this->assertArrayHasKey('name', $parsed);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertSame(
            ['description', 'installation', 'faq', 'screenshots', 'changelog'],
            array_keys($parsed['sections'])
        );

        $this->assertBase($parsed);
    }

    public function testParseString(): void
    {
        $content = <<<'EOF'
Test Plugin

Stable tag: 0.2.0
Tested up to: 6.6.1
Requires at least: 5.9
Requires PHP: 8.2
License: GPLv3
License URI: https://www.gnu.org/licenses/licenses.html
Tags: basic, sample
Contributors: gaft
Donate link: https://www.paypal.me/GAFT

Here is a short description of the plugin.
EOF;

        $parsed = Parser::parse($content);

        $this->assertArrayHasKey('name', $parsed);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertEmpty($parsed['sections']);
        $this->assertBase($parsed);
    }

    /** @param ParsedContent $parsed */
    protected function assertBase(array $parsed): void
    {
        unset($parsed['sections']);
        $this->assertEquals(
            [
                'name' => 'Test Plugin',
                'tested' => '6.6.1',
                'requires' => '5.9',
                'requires_php' => '8.2',
                'stable_tag' => '0.2.0',
                'contributors' => 'gaft',
                'donate_link' => 'https://www.paypal.me/GAFT',
                'license_uri' => 'https://www.gnu.org/licenses/licenses.html',
                'license' => 'GPLv3',
                'tags' => 'basic, sample',
                'short_description' => 'Here is a short description of the plugin.',
            ],
            $parsed
        );
    }

    /** @return array<int, string[]> */
    public static function forTestParseInvalid(): array
    {
        return [
            [ '' ],
            [ "\n" ],
            [ 'lorem ipsum dolor sit amet' ],
            [ __DIR__ . '/fixtures/readme2.md' ],
        ];
    }

    #[DataProvider('forTestParseInvalid')]
    public function testParseInvalid(string $content): void
    {
        $parsed = Parser::parse($content);

        $this->assertArrayHasKey('name', $parsed);
        $this->assertEmpty($parsed['name']);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertEmpty($parsed['short_description']);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertEmpty($parsed['sections']);
    }

    public function testParsePlugin(): void
    {
        $parsed = Parser::parse(__DIR__ . '/fixtures/plugin.php');

        $this->assertArrayHasKey('name', $parsed);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertNotEmpty($parsed['sections']);
        $this->assertArrayHasKey('other_notes', $parsed['sections']);

        $other_notes = json_decode($parsed['sections']['other_notes'], true);

        $this->assertArrayHasKey('Plugin URI', $other_notes);
        $this->assertArrayHasKey('Author', $other_notes);
        $this->assertArrayHasKey('Author URI', $other_notes);
        $this->assertArrayHasKey('Text Domain', $other_notes);
        unset($parsed['sections']['other_notes']);
        $this->assertEquals(
            [
                'name' => 'Test Plugin',
                'stable_tag' => '0.2.0',
                'short_description' => 'Here is a short description of the plugin.',
                'requires' => '5.9',
                'requires_php' => '8.2',
                'sections' => [],
            ],
            $parsed
        );
    }

    public function testParsePluginMetadata(): void
    {
        $parsed = Parser::parse(__DIR__ . '/fixtures/metadata.php');

        $this->assertArrayHasKey('name', $parsed);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertNotEmpty($parsed['sections']);
        $this->assertArrayHasKey('other_notes', $parsed['sections']);
        $this->assertNotEmpty($parsed['sections']['other_notes']);

        $other_notes = json_decode($parsed['sections']['other_notes'], true);

        $this->assertArrayHasKey('Plugin URI', $other_notes);
        $this->assertArrayHasKey('Author', $other_notes);
        $this->assertArrayHasKey('Author URI', $other_notes);
        $this->assertArrayHasKey('Text Domain', $other_notes);
        $this->assertArrayHasKey('Domain Path', $other_notes);
        $this->assertArrayHasKey('Network', $other_notes);
        $this->assertArrayHasKey('Update URI', $other_notes);
        $this->assertArrayHasKey('Requires Plugins', $other_notes);
    }
}
