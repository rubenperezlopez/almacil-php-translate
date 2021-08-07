# 🈳 Almacil PHP Translate

This is a library written in PHP to internationalize websites and apps made with PHP.


<div style="text-align:center;border-radius:10px;border:1px solid grey;padding:16px;">
    Do you want to contribute? Buy me a coffee:
    <br>
    <a href="https://www.paypal.com/paypalme/rubenperezlopez/1" target="_blank" style="border:0;background-color:black;color:white;border-radius:6px;padding:6px;font-size:16px;text-decoration:none;">
    <span style="font-size:22px;position:relative;top:2px;">☕️</span>
    <span style="margin:0 6px;">DONATE 1€</span>
    <span style="font-size:18px;position:relative;top:2px;">🙏</span>
    </a>
</div>

## Features
- Translate from json files
- Translate words or phrases on the fly with *Google Translat*e and store to json files

## Installation
Installation is possible using Composer
```bash
composer requiere almacil/php-translate
```
## Usage
Create an instance of \Almacil\Translate:
```php
// Require composer autoloader
require __DIR__ . '/vendor/autoload.php';

// Language to translate from auto detectable language
$lang = 'en';

// Directory containing the json files with the translations
$directory = __DIR__ . '/i18n';

// We want the library to search for the translations that it cannot find in the files and to include the translations in the files
$findMissingTranslations = true;

// Create de instance
$translate = new \Almacil\Translate($lang, $directory, $findMissingTranslations);
echo $translate->get('Hola mundo!'); // Hello world!
```
Create a function with a short name for ease:
```php
// ... after the setup

function t($text, $params = null) {
    global $translate;
    return $translate->get($text, $params);
}

echo '<p>' . t('Hola mundo!') . '</p>'; // <p>Hello World!</p>
```

## Params
We can include parameters in translations like this:
```json
// ./i18n/en.json
{
    "hola-name": "Hello {{name}}!"
}
```
```php
// Some PHP file
echo '<p>' . t('hola-name', array('name' => 'Rubén')) . '</p>'; // <p>Hello Rubén!</p>
```

<br>
<div style="text-align:center;border-radius:10px;border:1px solid grey;padding:16px;">
    Do you want to contribute? Buy me a coffee:
    <br>
    <a href="https://www.paypal.com/paypalme/rubenperezlopez/1" target="_blank" style="border:0;background-color:black;color:white;border-radius:6px;padding:6px;font-size:16px;text-decoration:none;">
    <span style="font-size:22px;position:relative;top:2px;">☕️</span>
    <span style="margin:0 6px;">DONATE 1€</span>
    <span style="font-size:18px;position:relative;top:2px;">🙏</span>
    </a>
</div>

<div style="text-align:center;color:grey;font-size:12px;padding:6px;">
    Made with ❤️ by developer for developers
</div>