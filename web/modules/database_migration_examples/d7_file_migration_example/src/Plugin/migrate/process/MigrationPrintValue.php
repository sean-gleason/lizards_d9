<?php

namespace Drupal\d7_file_migration_example\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *	id = "print_value"
 * 
 * )
**/

class MigrationPrintValue extends ProcessPluginBase {

	public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property){

		if(is_array($value)){

			$keys = array_keys($value);

			\Drupal::logger('d7_file_migration_example')->alert('asg - array - keys=['. implode(', ', $keys) .'] values=[' . implode(', ', $value) . ']');
			// print('console found');
		} else {
			\Drupal::logger('d7_file_migration_example')->alert('asg - string - ' . $value);
			// print('console found');
		}
		 
		 // \Drupal::logger('awf_taxonomy_migration')->alert('asg migration found / triggered. v6');

		return $value;

	}

}
