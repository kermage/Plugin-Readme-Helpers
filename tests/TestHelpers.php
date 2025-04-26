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
        'PluginURI' => 'https://github.com/kermage/Plugin-Readme-Helpers',
        'Author' => 'Gene Alyson Fortunado Torcende',
        'AuthorURI' => 'https://github.com/kermage',
        'TextDomain' => 'test-plugin',
    ];

    public const FULL_METADATA = [
        'DomainPath' => 'languages',
        'Network' => 'true',
        'UpdateURI' => 'https://github.com/kermage/Plugin-Readme-Helpers',
        'RequiresPlugins' => 'hello-dolly',
    ];

    public static function get(string $file): string
    {
        return implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', $file]);
    }
}
