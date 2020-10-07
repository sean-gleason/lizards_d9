<?php

namespace Drupal\d7_node_migration_example\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Maps D7 location values to D8 address values.
 *
 * Example:
 *
 * @code
 * process:
 *   field_address:
 *    plugin: location_to_address
 *    source: field_location
 * @endcode
 *
 * @MigrateProcessPlugin(
 *   id = "location_to_address"
 * )
 */
class LocationToAddress extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   * $value is the array containing the lid for this location
   */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        $lid = $value['lid'];
        $db = \Drupal\Core\Database\Database::getConnection("default", "migrate");
        $location = $db->select('location', 'l')
          ->where('l.lid = ' . $lid)
          ->fields('l', array(
            'country',
            'province',
            'city',
            'postal_code',
            'street',
            'additional'))
          ->execute()
          ->fetchAssoc();
        $address = [
          'country_code' => strtoupper($location['country']),
          'administrative_area' => $location['province'],
          'locality' => $location['city'],
          'postal_code' => $location['postal_code'],
          'address_line1' => $location['street'],
          'address_line2' => $location['additional'],
        ];
        return $address;
    }
}
