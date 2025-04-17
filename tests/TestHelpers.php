<?php

namespace Tests;

final class TestHelpers
{
    public const COMMON_DATA = [
        'name' => 'Test Plugin',
        'stable_tag' => '0.2.0',
        'short_description' => 'Here is a short description of the plugin.',
        'requires' => '5.9',
        'requires_php' => '8.2',
    ];

    public const NON_METADATA = [
        'tested' => '6.6.1',
        'contributors' => 'gaft',
        'donate_link' => 'https://www.paypal.me/GAFT',
        'license_uri' => 'https://www.gnu.org/licenses/licenses.html',
        'license' => 'GPLv3',
        'tags' => 'basic, sample',
    ];

    public const BASIC_METADATA = [
        'Plugin URI' => 'https://github.com/kermage/Plugin-Readme-Helpers',
        'Author' => 'Gene Alyson Fortunado Torcende',
        'Author URI' => 'https://github.com/kermage',
        'Text Domain' => 'test-plugin',
    ];

    public const FULL_METADATA = [
        'Domain Path' => 'languages',
        'Network' => 'true',
        'Update URI' => 'https://github.com/kermage/Plugin-Readme-Helpers',
        'Requires Plugins' => 'hello-dolly',
    ];

    public static function get(string $file): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $file]);
    }
}
