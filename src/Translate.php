<?php

/**
 * Translate.php
 *
 *
 * @category   Translate
 * @author     Rubén Pérez López
 * @date       30/04/2018
 * @copyright  2018 Rubén Pérez López
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    03/08/2021 v4.0
 * @link       www.rubenperezlopez.com
 */

namespace Almacil;

class Translate
{
  private $path = __DIR__ . '/../i18n';
  private $lang = 'en';
  private $findMissingTranslations = false;

  private $translations;
  private $alternativeTranslations;

  public function __construct($lang = 'en', $path = __DIR__ . '/../i18n', $findMissingTranslations = false, $alternativeLang = 'en')
  {
    $this->path = implode('/', explode('//', $path . '/'));;
    $this->lang = $lang;
    $this->findMissingTranslations = $findMissingTranslations;

    if (!file_exists($path)) {
      mkdir($path);
    }

    $this->translations = new \stdClass();
    $this->getPath($this->path, $lang, 'translations');

    $this->alternativeTranslations = new \stdClass();
    $this->getPath($this->path, $alternativeLang, 'alternativeTranslations');
  }

  public function t($text, $params = null)
  {
    return $this->get($text, $params);
  }

  public function get($text, $params = null)
  {
    if ($this->translations->{$text} == '') {
      if ($this->findMissingTranslations) {
        $resp = $this->missingTranslation($text);
      } else {
        $resp = $text;
      }
    } else {
      $resp = $this->translations->{$text};
      if (isset($params)) {
        foreach ($params as $clave => $valor) {
          if (isset($valor) && isset($clave)) {
            $resp = implode($valor, explode('{{' . $clave . '}}', $resp));
          }
        }
      }
    }
    return str_replace("'", "&#39;", $resp);
  }

  private function getPath($path, $lang, $varName = 'translations', $prefix = '')
  {

    if (file_exists($path . $lang . '.json')) {
      $translations = $this->getFile($path . $lang . '.json');
      $keys = array_keys((array)$translations);
      for ($k = 0; $k < count($keys); $k++) {
        $this->{$varName}->{$prefix . $keys[$k]} = $translations->{$keys[$k]};
      }
    }

    // Modules
    $files = scandir($path);
    for ($i = 0; $i < count($files); $i++) {

      if (is_dir($path . $files[$i]) && $files[$i] != '.' && $files[$i] != '..') {
        $this->getPath($path . $files[$i] . '/', $lang, $varName, $prefix . $files[$i] . '.');
      }
    }
  }

  public function getFileName($path)
  {
    return $path . $this->lang . '.json';
  }

  private function missingTranslation($text)
  {
    $translations = $this->getFile($this->getFileName($this->path));

    $gt = json_decode($this->callAPI('GET', 'http://traductor.almacil.com/api/?lng=' . urlencode($this->lang) . '&txt=' . urlencode($text), false));
    $translations->{$text} = $gt->traduccion;

    $handle = fopen($this->getFileName($this->path), "w");
    fwrite($handle, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $this->translations->{$text} = $gt->traduccion;
    return $this->translations->{$text};
  }


  private static function getFile($file)
  {
    if (!file_exists($file)) {
      $fileData = new \stdClass();
    } else {
      $fileContent = file_get_contents($file);
      if ($fileContent == '') {
        $fileData = new \stdClass();
      } else {
        $fileData = json_decode($fileContent);
      }
    }
    return $fileData;
  }
  private static function callAPI($method, $url, $data)
  {
    $curl = curl_init();
    switch ($method) {
      case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
      case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data) {
          curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        break;
      default:
        if ($data) {
          $url = sprintf("%s?%s", $url, http_build_query($data));
        }
    }

    // OPTIONS:
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    // EXECUTE:
    $result = curl_exec($curl);

    if (!$result) {
      die("Connection Failure");
    }
    curl_close($curl);
    return $result;
  }
}
