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

```php
kermage\PluginReadmeHelpers\ParsedContent Object
(
    [name] => string
    [stable_tag] => string
    [short_description] => string
    [requires] => string
    [requires_php] => string
    [tested] => string
    [contributors] => string[]
    [donate_link] => string
    [license_uri] => string
    [license] => string
    [tags] => string[]
    [sections] => array<string, string>
    [metadata] => kermage\PluginReadmeHelpers\Metadata Object
        (
            [Name] => string
            [PluginURI] => string
            [Version] => string
            [Description] => string
            [Author] => string
            [AuthorURI] => string
            [TextDomain] => string
            [DomainPath] => string
            [Network] => string
            [RequiresWP] => string
            [RequiresPHP] => string
            [UpdateURI] => string
            [RequiresPlugins] => string[]
            [Title] => string
            [AuthorName] => string
        )
)
```

#### See [supported formats](./tests/fixtures) in action:

```bash
composer install
composer run test
```
