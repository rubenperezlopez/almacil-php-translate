![GitHub last commit](https://img.shields.io/github/last-commit/rubenperezlopez/almacil-php-translate)
![GitHub tag (latest by date)](https://img.shields.io/github/v/tag/rubenperezlopez/almacil-php-translate?label=last%20version)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/almacil/php-translate)
![GitHub](https://img.shields.io/github/license/rubenperezlopez/almacil-php-translate)
# 🈳 Almacil PHP Translate

This is a library written in PHP to internationalize websites and apps made with PHP.

<br>

*Do you want to contribute?*<br>
[![Donate 1€](https://img.shields.io/badge/Buy%20me%20a%20coffee-1%E2%82%AC-brightgreen?logo=buymeacoffee&logoColor=white&labelColor=grey&style=for-the-badge
)](https://www.paypal.com/paypalme/rubenperezlopez/1?target=_blank)

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

*Do you want to contribute?*<br>
[![Donate 1€](https://img.shields.io/badge/Buy%20me%20a%20coffee-1%E2%82%AC-brightgreen?logo=buymeacoffee&logoColor=white&labelColor=grey&style=for-the-badge
)](https://www.paypal.com/paypalme/rubenperezlopez/1?target=_blank)

---

Made with ❤️ by developer for developers
