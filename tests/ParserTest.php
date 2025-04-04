<?php

/**
 * @package Plugin Readme Helpers
 */

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use kermage\PluginReadmeHelpers\Parser;

final class ParserTest extends TestCase
{
    public function testParse(): void
    {
        $parsed = (new Parser())->parse((string) file_get_contents(__DIR__ . '/fixtures/readme.txt'));

        $this->assertArrayHasKey('sections', $parsed);
        $this->assertSame(
            ['description', 'installation', 'faq', 'screenshots', 'changelog'],
            array_keys($parsed['sections'])
        );

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
}
