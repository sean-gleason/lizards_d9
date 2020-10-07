<?php

namespace Drupal\d7_media_migration_example\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\d7\FieldableEntity;

/**
 * Use D7 database files managed table to create Media entities based on type.
 *
 * Extends FieldableEntity to gain access to the getFields method.
 *
 * The id to use in the migration yml source plugin definition.
 *
 * @MigrateSource(
 *   id = "file_to_media_example",
 *   source_module = "file",
 * )
 */
class FileToMediaExample extends FieldableEntity {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Source Data is queried from the 'files_managed' table.
    $query = $this->select('file_managed', 'fm')
      ->fields('fm');
    // Only get a specific type of file specified in the migration yml source.
    if (isset($this->configuration['type'])) {
      $query->condition('fm.type', $this->configuration['type']);
    }
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'fid' => $this->t('File Id'),
      'filename' => $this->t('File Name'),
      'uri' => $this->t('File URI'),
      'filemime' => $this->t('File Mimetype'),
      'filesize' => $this->t('File Size'),
      'status' => $this->t('File Status'),
      'timestamp' => $this->t('File Timestamp'),
      'type' => $this->t('File Type'),
      'uid' => $this->t('File uploaded by'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   *
   * Assigns the unique key for each migration.
   */
  public function getIds() {
    return [
      'fid' => [
        'type' => 'integer',
        'alias' => 'f',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   *
   * The prepareRow method helps get additional data that wasn't included in the
   * initial query, like field data on a node.
   *
   * Find a node in the D7 database by matching the current fid being migrated,
   * and use found node gives access to any field data for the particular nid.
   * To new field, add the field to the yml file, i.e. field_credit.
   */
  public function prepareRow(Row $row) {
    if (parent::prepareRow($row)) {
      $file_id = $row->getSourceProperty('fid');
      if (isset($this->configuration['type']) && $this->configuration['type'] === 'audio') {
        $query = $this->select('field_data_field_soundtrack', 'fs');
        $query->join('node', 'n', 'n.nid = fs.entity_id');
        $query->condition('fs.field_soundtrack_fid', $file_id);

        $node_fields = $query->fields('n')
          ->execute()
          ->fetchAll();

        if (isset($node_fields[0])) {
          foreach (array_keys($this->getFields('node', 'lizard')) as $field) {
            $row->setSourceProperty($field, $this->getFieldValues('node', $field, $node_fields[0]['nid']));
          }
        }
      }
      elseif (isset($this->configuration['type']) && $this->configuration['type'] === 'image') {
        $query = $this->select('field_data_field_image', 'fc');
        $query->join('node', 'n', 'n.nid = fc.entity_id');
        $query->condition('fc.field_image_fid', $file_id);

        $node_fields = $query->fields('n')
          ->execute()
          ->fetchAll();

        if (isset($node_fields[0])) {
          foreach (array_keys($this->getFields('node', 'lizard')) as $field) {
            $row->setSourceProperty($field, $this->getFieldValues('node', $field, $node_fields[0]['nid']));
          }
        }
      }

    }
    return parent::prepareRow($row);
  }

}
