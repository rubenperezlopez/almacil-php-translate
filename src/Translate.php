<?php

/**
 * Translate.php
 *
 *
 * @category   Translate
 * @author     Rubén Pérez López
 * @date       30/04/2018
 * @copyright  2023 Rubén Pérez López
 * @license    http://www.php.net/license/3_0.txt  PHP License 3.0
 * @version    13/10/2023 v5.0
 * @link       www.rubenperezlopez.com
 */

namespace Almacil;

class Translate
{
  private $path = __DIR__ . '/../i18n';
  private $lang = 'en';
  private $findMissingTranslations = false;
  private $isInContextEditorActivated = false;

  private $translations;
  private $alternativeTranslations;

  public function __construct($lang = 'en', $path = __DIR__ . '/../i18n', $findMissingTranslations = false, $isInContextEditorActivated = false, $alternativeLang = 'en')
  {
    $this->path = implode('/', explode('//', $path . '/'));;
    $this->lang = $lang;
    $this->findMissingTranslations = $findMissingTranslations;
    $this->isInContextEditorActivated = $isInContextEditorActivated;

    if (!file_exists($path)) {
      mkdir($path);
    }

    $this->translations = new \stdClass();
    $this->getPath($this->path, $lang, 'translations');

    $this->alternativeTranslations = new \stdClass();
    $this->getPath($this->path, $alternativeLang, 'alternativeTranslations');
  }

  public function setInContextEditor($value)
  {
    $this->isInContextEditorActivated = $value;
  }

  public function t($key, $params = null)
  {
    return $this->get($key, $params);
  }

  public function get($key, $params = null)
  {
    $modulo = substr($key, 0, strpos($key, '.'));
    $modulos = [
      "common",
      "iframe",
      "canal",
      "entradas",
      "evento",
      "explore",
      "form",
      "listas",
      "micuenta",
      "mistickets",
      "reservas"
    ];
    if (in_array($modulo, $modulos)) {
      $key = substr($key, strpos($key, '.') + 1);
    }
    if ($this->isInContextEditorActivated) {
      return '{{__phrase_' . $key . '__}}';
    }
    if ($this->translations->{$key} == '') {
      if ($this->findMissingTranslations) {
        $resp = $this->missingTranslation($key);
      } else {
        $resp = $key;
      }
    } else {
      $resp = $this->translations->{$key};
      if (isset($params)) {
        foreach ($params as $clave => $valor) {
          if (isset($valor) && isset($clave)) {
            $resp = implode($valor, explode('{{' . $clave . '}}', $resp));
          }
        }
      }
    }

    $text = str_replace("'", "&#39;", $resp);
    return $text;
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

  private function missingTranslation($key)
  {
    $translations = $this->getFile($this->getFileName($this->path));

    $gt = json_decode($this->callAPI('GET', 'http://traductor.almacil.com/api/?lng=' . urlencode($this->lang) . '&txt=' . urlencode($key), false));
    $translations->{$key} = $gt->traduccion;

    $handle = fopen($this->getFileName($this->path), "w");
    fwrite($handle, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $this->translations->{$key} = $gt->traduccion;
    return $this->translations->{$key};
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
