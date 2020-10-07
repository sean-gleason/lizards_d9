<?php
/**
 * @file
 * Containts Drupal\d7_reset_button\Form\ResetForm.php 
 */

namespace Drupal\d7_reset_button\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

class ResetForm extends ConfigFormBase {

    public function getEditableConfigNames() {

      return 'd7_reset_buttom_form_config.settings';

    }

    public function getFormId() {

      return 'd7_reset_buttom_form';

    }


    public function buildForm(array $form, FormStateInterface $form_state) {

      $form['intro'] = [
        '#type' => 'item',
        '#markup' => 'Click the button below to reset all the content migrated from the D7 site. Intended to make the same site more user friendly.'
      ];

      return parent::buildForm($form, $form_state);

    }

    public function submitForm(array &$form, FormStateInterface $form_state) {

        parent::submitForm($form, $form_state);

        $storage_handler = \Drupal::entityTypeManager();
        $nids = $storage_handler->getStorage('node')->getQuery()->condition('type', 'lizard')->execute();
        $vids = $storage_handler->getStorage('taxonomy_vocabulary')->getQuery()->condition('vid', 'region')->execute();
        $mids = $storage_handler->getStorage('media')->getQuery()->execute();
        $fids = $storage_handler->getStorage('file')->getQuery()->execute();

        if(!empty($nids)){
          foreach( array_chunk($nids, 50) as $nid) {

            $nodes = $storage_handler->getStorage('node')->loadMultiple($nid);
            $storage_handler->getStorage('node')->delete($nodes);

          }
        }

        if(!empty($vids)){
          foreach( array_chunk($vids, 50) as $vid){

            $terms = $storage_handler->getStorage('taxonomy_vocabulary')->loadMultiple($vid);
            $storage_handler->getStorage('taxonomy_vocabulary')->delete($terms);

          }
        }

        if(!empty($mids)) {
          foreach(array_chunk($mids, 50) as $mid) {

            $media = $storage_handler->getStorage('media')->loadMultiple($mid);
            $storage_handler->getStorage('media')->delete($media);

          }
        }

        if(!empty($fids)) {
          foreach( array_chunk($fids, 50) as $fid) {

            $file = $storage_handler->getStorage('file')->loadMultiple($fid);
            $storage_handler->getStorage('file')->delete($file);

          }
        }

        // migration table
        $connection = \Drupal::service('database');
        $connection->delete('migrate_map_d7_node_migration_example')->execute();
        $connection->delete('migrate_message_d7_node_migration_example')->execute();
        $connection->delete('migrate_map_d7_taxonomy_term_migration_example')->execute();
        $connection->delete('migrate_message_d7_taxonomy_term_migration_example')->execute();
        $connection->delete('migrate_map_d7_taxonomy_vocabulary_migration_example')->execute();
        $connection->delete('migrate_message_d7_taxonomy_vocabulary_migration_example')->execute();
        $connection->delete('migrate_map_image_media_migration_example')->execute();
        $connection->delete('migrate_message_image_media_migration_example')->execute();
        $connection->delete('migrate_map_audio_media_migration_example')->execute();
        $connection->delete('migrate_message_audio_media_migration_example')->execute();
        $connection->delete('migrate_map_d7_file_migration_example')->execute();
        $connection->delete('migrate_message_d7_file_migration_example')->execute();


    }

}
