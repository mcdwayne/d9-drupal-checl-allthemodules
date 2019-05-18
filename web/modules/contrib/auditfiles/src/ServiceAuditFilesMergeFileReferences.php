<?php

namespace Drupal\auditfiles;

use Drupal\Core\Database\Database;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\file\Entity\File;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Define all methods that used in merge file references functionality.
 */
class ServiceAuditFilesMergeFileReferences {

  use StringTranslationTrait;

  /**
   * Define constructor for string translation.
   */
  public function __construct(TranslationInterface $translation) {
    $this->stringTranslation = $translation;
  }

  /**
   * Retrieves the file IDs to operate on.
   *
   * @return array
   *   The file IDs.
   */
  public function auditfilesMergeFileReferencesGetFileList() {
    $config = \Drupal::config('auditfiles.settings');
    $connection = Database::getConnection();
    $result_set = [];
    $query = 'SELECT fid, filename FROM {file_managed} ORDER BY filename ASC';
    $maximum_records = $config->get('auditfiles_report_options_maximum_records') ? $config->get('auditfiles_report_options_maximum_records') : 250;
    if ($maximum_records > 0) {
      $query .= ' LIMIT ' . $maximum_records;
    }
    $files = $connection->query($query)->fetchAllKeyed();
    $show_single_file_names = $config->get('auditfiles_merge_file_references_show_single_file_names') ? $config->get('auditfiles_merge_file_references_show_single_file_names') : 0;
    foreach ($files as $file_id => $file_name) {
      if ($show_single_file_names) {
        $result_set[] = $file_name;
      }
      else {
        $query2 = 'SELECT COUNT(fid) count FROM {file_managed} WHERE filename = :filename AND fid != :fid';
        $results2 = $connection->query(
          $query2,
          [
            ':filename' => $file_name,
            ':fid' => $file_id,
          ]
        )->fetchField();
        if ($results2 > 0) {
          $result_set[] = $file_name;
        }
      }
    }
    return array_unique($result_set);
  }

  /**
   * Retrieves information about an individual file from the database.
   *
   * @param int $file_name
   *   The ID of the file to prepare for display.
   * @param string $date_format
   *   The date format to prepair for display.
   *
   * @return array
   *   The row for the table on the report, with the file's
   *   information formatted for display.
   */
  public function auditfilesMergeFileReferencesGetFileData($file_name, $date_format) {
    $connection = Database::getConnection();
    $fid_query = 'SELECT fid FROM {file_managed} WHERE filename = :filename';
    $fid_results = $connection->query($fid_query, [':filename' => $file_name])->fetchAll();
    if (count($fid_results) > 0) {
      $references = '<ul>';
      foreach ($fid_results as $fid_result) {
        $query = $connection->select('file_managed', 'fm');
        $query->condition('fm.fid', $fid_result->fid);
        $query->fields('fm', [
          'fid',
          'uid',
          'filename',
          'uri',
          'filemime',
          'filesize',
          'created',
          'status',
        ]);
        $results = $query->execute()->fetchAll();
        $file = $results[0];
        $references .= '<li>' . $this->t('<strong>Fid: </strong> %id , <strong>Name : </strong> %file , <strong>File URI: </strong> %uri , <strong>Date: </strong> %date',
          [
            '%id' => $file->fid,
            '%file' => $file->filename,
            '%uri' => $file->uri,
            '%date' => format_date($file->created, $date_format),
          ]
        ) . '</li>';
      }
      $references .= '</ul>';
      $usage = new FormattableMarkup($references, []);
      return [
        'filename' => $file_name,
        'references' => $usage,
      ];
    }
  }

  /**
   * Returns the header to use for the display table.
   *
   * @return array
   *   The header to use.
   */
  public function auditfilesMergeFileReferencesGetHeader() {
    return [
      'filename' => [
        'data' => $this->t('Name'),
      ],
      'references' => [
        'data' => $this->t('File IDs using the filename'),
      ],
    ];
  }

  /**
   * Creates the batch for merging files.
   *
   * @param int $file_being_kept
   *   The file ID of the file to merge the others into.
   * @param array $files_being_merged
   *   The list of file IDs to be processed.
   *
   * @return array
   *   The definition of the batch.
   */
  public function auditfilesMergeFileReferencesBatchMergeCreateBatch($file_being_kept, array $files_being_merged) {
    $batch['error_message'] = $this->t('One or more errors were encountered processing the files.');
    $batch['finished'] = '\Drupal\auditfiles\AuditFilesBatchProcess::auditfilesMergeFileReferencesBatchFinishBatch';
    $batch['progress_message'] = $this->t('Completed @current of @total operations.');
    $batch['title'] = $this->t('Merging files');
    $operations = $file_ids = [];
    foreach ($files_being_merged as $file_id => $file_info) {
      if ($file_id != 0) {
        $file_ids[] = $file_id;
      }
    }
    foreach ($file_ids as $file_id) {
      if ($file_id != $file_being_kept) {
        $operations[] = [
          '\Drupal\auditfiles\AuditFilesBatchProcess::auditfilesMergeFileReferencesBatchMergeProcessBatch',
          [
            $file_being_kept,
            $file_id,
          ],
        ];
      }
    }
    $batch['operations'] = $operations;
    return $batch;
  }

  /**
   * Deletes the specified file from the file_managed table.
   *
   * @param int $file_being_kept
   *   The file ID of the file to merge the other into.
   * @param int $file_being_merged
   *   The file ID of the file to merge.
   */
  public function auditfilesMergeFileReferencesBatchMergeProcessFile($file_being_kept, $file_being_merged) {
    $connection = Database::getConnection();
    $file_being_kept_results = $connection->select('file_usage', 'fu')
      ->fields('fu', ['module', 'type', 'id', 'count'])
      ->condition('fid', $file_being_kept)
      ->execute()
      ->fetchAll();
    if (empty($file_being_kept_results)) {
      $message = $this->t('There was no file usage data found for the file you choose to keep. No changes were made.');
      drupal_set_message($message, 'warning');
      return;
    }
    $file_being_kept_data = reset($file_being_kept_results);
    $file_being_kept_name_results = $connection->select('file_managed', 'fm')
      ->fields('fm', ['filename'])
      ->condition('fid', $file_being_kept)
      ->execute()
      ->fetchAll();
    $file_being_merged_results = $connection->select('file_usage', 'fu')
      ->fields('fu', ['module', 'type', 'id', 'count'])
      ->condition('fid', $file_being_merged)
      ->execute()
      ->fetchAll();
    if (empty($file_being_merged_results)) {
      $message = $this->t(
        'There was an error retrieving the file usage data from the database for file ID %fid. Please check the files in one of the other reports. No changes were made for this file.',
        ['%fid' => $file_being_merged]
      );
      drupal_set_message($message, 'warning');
      return;
    }
    $file_being_merged_data = reset($file_being_merged_results);
    $file_being_merged_uri_results = $connection->select('file_managed', 'fm')
      ->fields('fm', ['uri'])
      ->condition('fid', $file_being_merged)
      ->execute()
      ->fetchAll();
    $file_being_merged_uri = reset($file_being_merged_uri_results);
    if ($file_being_kept_data->id == $file_being_merged_data->id) {
      $file_being_kept_data->count += $file_being_merged_data->count;
      // Delete the unnecessary entry from the file_usage table.
      $connection->delete('file_usage')
        ->condition('fid', $file_being_merged)
        ->execute();
      // Update the entry for the file being kept.
      $connection->update('file_usage')
        ->fields(['count' => $file_being_kept_data->count])
        ->condition('fid', $file_being_kept)
        ->condition('module', $file_being_kept_data->module)
        ->condition('type', $file_being_kept_data->type)
        ->condition('id', $file_being_kept_data->id)
        ->execute();
    }
    else {
      $connection->update('file_usage')
        ->fields(['fid' => $file_being_kept])
        ->condition('fid', $file_being_merged)
        ->condition('module', $file_being_merged_data->module)
        ->condition('type', $file_being_merged_data->type)
        ->condition('id', $file_being_merged_data->id)
        ->execute();
      // Update any fields that might be pointing to the file being merged.
      $this->auditfilesMergeFileReferencesUpdateFileFields($file_being_kept, $file_being_merged);
    }
    // Delete the unnecessary entries from the file_managed table.
    $connection->delete('file_managed')
      ->condition('fid', $file_being_merged)
      ->execute();
    // Delete the duplicate file.
    if (!empty($file_being_merged_uri->uri)) {
      file_unmanaged_delete($file_being_merged_uri->uri);
    }
  }

  /**
   * Updates any fields that might be pointing to the file being merged.
   *
   * @param int $file_being_kept
   *   The file ID of the file to merge the other into.
   * @param int $file_being_merged
   *   The file ID of the file to merge.
   */
  public function auditfilesMergeFileReferencesUpdateFileFields($file_being_kept, $file_being_merged) {
    $file_being_merged_fields = file_get_file_references(File::load($file_being_merged), NULL, EntityStorageInterface::FIELD_LOAD_REVISION, NULL);
    if (empty($file_being_merged_fields)) {
      return;
    }
    foreach ($file_being_merged_fields as $field_name => $field_references) {
      foreach ($field_references as $entity_type => $type_references) {
        foreach ($type_references as $id => $reference) {
          $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($id);
          if ($entity) {
            $field_items = $entity->get($field_name)->getValue();
            $alt = $field_items[0]['alt'];
            $title = $field_items[0]['title'] ? $field_items[0]['title'] : '';
            foreach ($field_items as $item) {
              if ($item['target_id'] == $file_being_merged) {
                $file_object_being_kept = File::load($file_being_kept);
                  if (!empty($file_object_being_kept) && $entity->get($field_name)->getValue() != $file_being_kept) {
                    $entity->$field_name = [
                      'target_id' => $file_object_being_kept->id(),
                      'alt' => $alt,
                      'title' => $title,
                    ];
                  }
                $entity->save();
                break;
              }
            }
          }
        }
      }
    }
  }

}
