<?php

/**
 * Load services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Include the Pantheon-specific settings file.
 *
 * n.b. The settings.pantheon.php file makes some changes
 *      that affect all environments that this site
 *      exists in.  Always include this file, even in
 *      a local development environment, to ensure that
 *      the site settings remain consistent.
 */
include __DIR__ . "/settings.pantheon.php";

/**
 * Place the config directory outside of the Drupal root.
 */
$settings['config_sync_directory'] = dirname(DRUPAL_ROOT) . '/config';

/**
 * If there is a local settings file, then include it
 */
$local_settings = __DIR__ . "/settings.local.php";
if (file_exists($local_settings)) {
  include $local_settings;
}

/**
 * Always install the 'standard' profile to stop the installer from
 * modifying settings.php.
 */
$settings['install_profile'] = 'standard';

$settings['file_public_path'] = 'sites/default/files';

/**
 * Migration scripts for cloud environments. Set using Terminus Secrets plugin
 */

 if(isset($_ENV['PANTHEON_ENVIRONMENT'])){

    $secretsFile = $_SERVER['HOME'] . '/files/private/secrets.json';
    if(file_exists($secretsFile)) {
      $secrets = json_decode(file_get_contents($secretsFile), TRUE);
      
      if( !empty($secrets['migrate_source_db__database']) && 
        !empty($secrets['migrate_source_db__username']) && 
        !empty($secrets['migrate_source_db__pass']) && 
        !empty($secrets['migrate_source_db__host']) && 
        !empty($secrets['migrate_source_db__port']) ) {

          $databases['migrate']['default'] = [
            'database' => $secrets['migrate_source_db__database'],
            'username' => $secrets['migrate_source_db__username'],
            'password' => $secrets['migrate_source_db__pass'],
            'host' => $secrets['migrate_source_db__host'],
            'port' => $secrets['migrate_source_db__port'],
            'driver' => 'mysql',
            'prefix' => '',
            'collation' => 'utf8mb4_general_ci',
          ];
      }
    }
  } 