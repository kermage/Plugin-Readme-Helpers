<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use kermage\PluginReadmeHelpers\Parser;
use PHPUnit\Framework\Attributes\DataProvider;

final class ParserTest extends TestCase
{
    /** @return array<int, string[]> */
    public static function forTestParse(): array
    {
        return [
            [ TestHelpers::get('readme.txt') ],
            [ TestHelpers::get('readme.md') ],
            [ 'https://raw.githubusercontent.com/kermage/Plugin-Readme-Helpers/refs/heads/main/tests/fixtures/readme.txt' ], // phpcs:ignore Generic.Files.LineLength.TooLong
        ];
    }

    #[DataProvider('forTestParse')]
    public function testParse(string $file): void
    {
        $parsed = (array) Parser::parse($file);

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

        $parsed = (array) Parser::parse($content);

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
                ...TestHelpers::COMMON_DATA,
                ...TestHelpers::NON_METADATA,
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
            [ TestHelpers::get('readme2.md') ],
        ];
    }

    #[DataProvider('forTestParseInvalid')]
    public function testParseInvalid(string $content): void
    {
        $parsed = (array) Parser::parse($content);

        $this->assertArrayHasKey('name', $parsed);
        $this->assertEmpty($parsed['name']);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertEmpty($parsed['short_description']);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertEmpty($parsed['sections']);
    }

    /** @param ParsedContent $parsed */
    protected function assertPlugin(array $parsed, bool $full = false): void
    {
        $this->assertArrayHasKey('name', $parsed);
        $this->assertArrayHasKey('short_description', $parsed);
        $this->assertArrayHasKey('sections', $parsed);
        $this->assertNotEmpty($parsed['sections']);
        $this->assertArrayHasKey('other_notes', $parsed['sections']);
        unset($parsed['sections']);
        $this->assertEquals(
            [
                ...TestHelpers::COMMON_DATA,
                ...($full ? TestHelpers::NON_METADATA : []),
            ],
            $parsed
        );
    }

    public function testParsePlugin(): void
    {
        $parsed = (array) Parser::parse(TestHelpers::get('plugin.php'));

        $this->assertPlugin($parsed);
        $this->assertEquals(
            TestHelpers::BASIC_METADATA,
            json_decode($parsed['sections']['other_notes'], true)
        );
    }

    public function testParsePluginMetadata(): void
    {
        $parsed = (array) Parser::parse(TestHelpers::get('metadata.php'));

        $this->assertPlugin($parsed, true);
        $this->assertEquals(
            [
                ...TestHelpers::BASIC_METADATA,
                ...TestHelpers::FULL_METADATA,
            ],
            json_decode($parsed['sections']['other_notes'], true)
        );
    }
}
