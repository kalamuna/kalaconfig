<?php

namespace Drupal\kalaconfig;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\config_filter\Config\FilteredStorage;
use Drupal\config_split\Config\GhostStorage;
use Drupal\Core\Config\FileStorageFactory;

/**
 * Various helpful functions for use mainly by Drupal settings files.
 */
class KalaUtil {

  /**
   * The default status code to use across all the redirect functions.
   *
   * @var array
   */
  public static $defaultStatusCode = 302;

  /**
   * The default protocol to use across all the redirect functions.
   *
   * @var array
   */
  public static $defaultProtocol;

  /**
   * Issues an HTTP Location response to the browser and ends current request.
   *
   * @param string $protocol
   *   'http', 'https', or NULL to use the protocol of the current request.
   * @param string $host
   *   The destination host, or NULL to use the host of the current request.
   * @param string $path
   *   The destination URI, or NULL to use the URI of the current request.
   * @param int $status
   *   The HTTP status code to use in the redirection response; defaults to 302.
   */
  public static function redirect($protocol = NULL, $host = NULL, $path = NULL, $status = NULL) {

    // Don't break drush.
    if (PHP_SAPI === 'cli') {
      return;
    }

    // Prep the variables with defaults.
    foreach (array_keys(get_defined_vars()) as $var) {
      if (!empty($$var)) {
        switch ($var) {

          case 'protocol':
            $protocol = isset(static::$defaultProtocol)
              ? static::$defaultProtocol
              : (isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
                ? $_SERVER['HTTP_X_FORWARDED_PROTO']
                : 'http');
            break;

          case 'host':
            $host = $_SERVER['HTTP_HOST'];
            break;

          case 'path':
            $path = $_SERVER['REQUEST_URI'];
            break;

          case 'status':
            $status = static::$defaultStatusCode;
            break;
        }
      }
    }

    // Name transaction "redirect" in New Relic for improved reporting.
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    // Let Symfony issue the redirect.
    $response = new RedirectResponse("$protocol://$host{$path}", $status);
    $response->send();
  }

  /**
   * Forces use of SSL.
   */
  public static function enforceSSL() {
    if (!isset($_SERVER['HTTP_X_SSL']) || $_SERVER['HTTP_X_SSL'] !== 'ON') {
      static::redirect('https', NULL, NULL, 301);
    }
  }

  /**
   * Redirects wildcards (including root) to a single path on the same host.
   *
   * @param array $wildcard_to_path
   *   An array of destinations keyed by the wildcard paths to match.
   */
  public static function redirectWildcards(array $wildcard_to_path) {
    foreach ($wildcard_to_path as $wildcard => $path) {
      if (stripos($_SERVER['REQUEST_URI'], $wildcard) === 0) {
        static::redirect(NULL, NULL, $path);
      }
    }
  }

  /**
   * Exports a given config split to its corresponding folder.
   *
   * Code adapted from \Drupal\config_split\ConfigSplitCliService::ioExport().
   *
   * @param array $splits
   *   Machine names of the config splits to export. E.g., ['dev', 'test'].
   */
  public static function exportConfigSplits(array $splits) {
    $config_filter = \Drupal::service('plugin.manager.config_filter');
    $storage_factory = \Drupal::service('config_filter.storage_factory');
    $sync = FileStorageFactory::getSync();
    $config_split = \Drupal::service('config_split.cli');
    foreach ($splits as $split) {
      $plugin_id = "config_split:$split";
      $filter = $config_filter->getFilterInstance($plugin_id);
      $storage = $storage_factory->getFilteredStorage($sync, ['config.storage.sync'], [$plugin_id]);
      $split = new FilteredStorage(new GhostStorage($storage), [$filter]);
      $config_split->export($split);
    }
  }

  /**
   * Deletes a content type.
   *
   * @param string $name
   *   Machine name of the content type to delete.
   */
  public static function deleteContentType($name) {
    if ($type = \Drupal::entityManager()->getStorage('node_type')->load($name)) {
      $type->delete();
    }
    // Run cron to purge field tables from the database.
    \Drupal::service('cron')->run();
  }

  /**
   * Deletes all the entities of the given entity type.
   *
   * @param string $entity_type
   *   The entity type machine name.
   */
  public static function deleteAllEntitiesOfType($entity_type) {
    if (\Drupal::entityTypeManager()->hasHandler($entity_type, 'storage')) {
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
      if ($ids = $storage->getQuery()->execute()) {
        $entities = $storage->loadMultiple($ids);
        $storage->delete($entities);
      }
    }
  }

}
