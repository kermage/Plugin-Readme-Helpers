# Plugin Readme Helpers

> _"Set of classes for handling WordPress plugin readme files."_

Designed to work independently of the WordPress runtime.

## Parser

Accepts either a prepared string or path to a file (`.md`, `.txt`, or `.php`).

```php
use kermage\PluginReadmeHelpers\Parser;

$input  = '...';
$output = Parser::parse($input);

print_r($output);
```

### Output

```txt
kermage\PluginReadmeHelpers\ParsedContent Object
(
    [name] =>
    [stable_tag] =>
    [short_description] =>
    [requires] =>
    [requires_php] =>
    [tested] =>
    [contributors] =>
    [donate_link] =>
    [license_uri] =>
    [license] =>
    [tags] =>
    [sections] => Array
        (
        )

    [metadata] => kermage\PluginReadmeHelpers\Metadata Object
        (
            [Name] =>
            [PluginURI] =>
            [Version] =>
            [Description] =>
            [Author] =>
            [AuthorURI] =>
            [TextDomain] =>
            [DomainPath] =>
            [Network] =>
            [RequiresWP] =>
            [RequiresPHP] =>
            [UpdateURI] =>
            [RequiresPlugins] =>
            [Title] =>
            [AuthorName] =>
        )
)
```

#### See [supported formats](./tests/fixtures) in action:

```bash
composer install
composer run test
```
