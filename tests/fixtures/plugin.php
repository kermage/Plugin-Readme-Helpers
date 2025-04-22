<?php

/**
 * @package Test Plugin
 * @version 0.2.0
 */

declare(strict_types=1);

namespace TestPlugin;

use kermage\PluginReadmeHelpers\Parser;

/**
 * Plugin Name: Test Plugin
 * Plugin URI:  https://github.com/kermage/Plugin-Readme-Helpers
 * Version:     0.2.0
 * Description: Here is a short description of the plugin.
 * Author:      Gene Alyson Fortunado Torcende
 * Author URI:  https://github.com/kermage
 * Text Domain: test-plugin
 *
 * Requires at least: 5.9
 * Requires PHP:      8.2
 */

/**
 * Chaser: Water
 */
function parseable(string $content): bool
{
    $data = (array) Parser::parse($content);

    return ! empty($data['sections']);
}
