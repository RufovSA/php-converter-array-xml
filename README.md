# ConverterArrayXML

[![Latest Version](https://img.shields.io/github/release/rufovS/php-converter-array-xml.svg?style=flat-square)](https://github.com/rufovS/php-converter-array-xml/releases)
[![Software License](https://img.shields.io/badge/license-BSD_3_Clause-brightgreen.svg?style=flat-square)](LICENSE.md)
<!--
![Tests](https://github.com/rufovS/php-converter-array-xml/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/rufov/converter-array-xml.svg?style=flat-square)](https://packagist.org/packages/rufov/converter-array-xml)
-->

The class is designed to convert an XML document into a PHP array and vice versa

## Installation

The package could be installed with composer:

```
composer require rufov/converter-array-xml
```

## Usage
```php
use RufovS\ConverterArrayXML\ConverterArrayXML;

$xml = <<<_XML
<?xml version="1.0"?>
<root xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <GoodGuy attr1="value">
        <name><![CDATA[<h1>Luke Skywalker</h1>]]></name>
        <weapon>Lightsaber</weapon>
    </GoodGuy>
    <BadGuy>
        <name>Sauron</name>
        <weapon>Evil Eye</weapon>
    </BadGuy>
</root>
_XML;

$result = ConverterArrayXML::xmlToArr($xml);
```

After running this piece of code ```$result``` will contain:
```php
$result = [
    'root' => [
        '_attributes' => [
                'xmlns:xs=' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ],
        'GoodGuy' => [
             '_attributes' => [
                'attr1' => 'value'
            ],
            'name' => [
                '_cdata' => '<h1>Luke Skywalker</h1>'
            ],
            'weapon' => 'Lightsaber'
        ],
        'BadGuy' => [
            'name' => 'Sauron',
            'weapon' => 'Evil Eye'
        ]
    ]
];
```

You can also convert an array to xml. This is done as follows:
```php
use RufovS\ConverterArrayXML\ConverterArrayXML;

$array = [
    'root' => [
        '_attributes' => [
                'xmlns:xs=' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance'
        ],
        'GoodGuy' => [
             '_attributes' => [
                'attr1' => 'value'
            ],
            'name' => [
                '_cdata' => '<h1>Luke Skywalker</h1>'
            ],
            'weapon' => 'Lightsaber'
        ],
        'BadGuy' => [
            'name' => 'Sauron',
            'weapon' => 'Evil Eye'
        ]
    ]
];

$result = ConverterArrayXML::arrayToXml($array);
```

At the end you will get the following xml document.
```xml
<?xml version="1.0"?>
<root xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <GoodGuy attr1="value">
        <name><![CDATA[<h1>Luke Skywalker</h1>]]></name>
        <weapon>Lightsaber</weapon>
    </GoodGuy>
    <BadGuy>
        <name>Sauron</name>
        <weapon>Evil Eye</weapon>
    </BadGuy>
</root>
```

## Testing

```bash
vendor/bin/phpunit
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The BSD-3-Clause license. Please see [License File](LICENSE.md) for more information.
