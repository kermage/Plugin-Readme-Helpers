<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace Tests;

use kermage\PluginReadmeHelpers\Metadata;
use PHPUnit\Framework\TestCase;
use kermage\PluginReadmeHelpers\ParsedContent;
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
        $parsed = Parser::parse($file);

        $this->assertBase($parsed);
        $this->assertObjectHasProperty('name', $parsed);
        $this->assertObjectHasProperty('short_description', $parsed);
        $this->assertObjectHasProperty('sections', $parsed);
        $this->assertObjectHasProperty('metadata', $parsed);
        $this->assertSame(
            ['description', 'installation', 'faq', 'screenshots', 'changelog'],
            array_keys($parsed->sections)
        );
        $this->assertInstanceOf(Metadata::class, $parsed->metadata);
        $this->assertEmpty(array_filter((array) $parsed->metadata));
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

        $this->assertBase($parsed);
        $this->assertObjectHasProperty('name', $parsed);
        $this->assertObjectHasProperty('short_description', $parsed);
        $this->assertObjectHasProperty('sections', $parsed);
        $this->assertObjectHasProperty('metadata', $parsed);
        $this->assertEmpty($parsed->sections);
        $this->assertInstanceOf(Metadata::class, $parsed->metadata);
        $this->assertEmpty(array_filter((array) $parsed->metadata));
    }

    protected function assertBase(ParsedContent $parsed): void
    {
        $parsed = (array) $parsed;
        unset($parsed['sections'], $parsed['metadata']);
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
        $parsed = Parser::parse($content);

        $this->assertObjectHasProperty('name', $parsed);
        $this->assertEmpty($parsed->name);
        $this->assertObjectHasProperty('short_description', $parsed);
        $this->assertEmpty($parsed->short_description);
        $this->assertObjectHasProperty('sections', $parsed);
        $this->assertEmpty($parsed->sections);
        $this->assertObjectHasProperty('metadata', $parsed);
        $this->assertInstanceOf(Metadata::class, $parsed->metadata);
        $this->assertEmpty(array_filter((array) $parsed->metadata));
    }

    protected function assertPlugin(ParsedContent $parsed, bool $full = false): void
    {
        $this->assertObjectHasProperty('name', $parsed);
        $this->assertObjectHasProperty('short_description', $parsed);
        $this->assertObjectHasProperty('sections', $parsed);
        $this->assertEmpty($parsed->sections);
        $this->assertObjectHasProperty('metadata', $parsed);
        $this->assertInstanceOf(Metadata::class, $parsed->metadata);
        $this->assertNotEmpty($parsed->metadata);

        $parsed = (array) $parsed;

        unset($parsed['sections'], $parsed['metadata']);
        $this->assertEquals(
            [
                ...TestHelpers::COMMON_DATA,
                ...($full ? TestHelpers::NON_METADATA : []),
            ],
            array_filter($parsed)
        );
    }

    public function testParsePlugin(): void
    {
        $parsed = Parser::parse(TestHelpers::get('plugin.php'));

        $this->assertPlugin($parsed);
        $this->assertEquals(
            [
                ...TestHelpers::commonMetadata(),
                ...TestHelpers::BASIC_METADATA,
            ],
            array_filter((array) $parsed->metadata)
        );
    }

    public function testParsePluginMetadata(): void
    {
        $parsed = Parser::parse(TestHelpers::get('metadata.php'));

        $this->assertPlugin($parsed, true);
        $this->assertEquals(
            [
                ...TestHelpers::commonMetadata(),
                ...TestHelpers::BASIC_METADATA,
                ...TestHelpers::FULL_METADATA,
            ],
            array_filter((array) $parsed->metadata)
        );
    }
}
