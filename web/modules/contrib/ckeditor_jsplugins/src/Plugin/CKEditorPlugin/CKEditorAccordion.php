<?php
/**
 * @file
 * Contains \Drupal\ckeditor_jsplugins\Plugin\CKEditorPlugin\CKEditorAccordion.
 */

namespace Drupal\ckeditor_jsplugins\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "CKEditorAccordion" plugin.
 *
 * @CKEditorPlugin (
 *   id = "accordion",
 *   label = @Translation("CKEditorAccordion"),
 *   module = "ckeditor_jsplugins"
 * )
 */
class CKEditorAccordion extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $config = array();
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'ckeditor_jsplugins') . '/js/plugins/accordion/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = drupal_get_path('module', 'ckeditor_jsplugins') . '/js/plugins/accordion/icons';
    return array(
      'Accordion' => array(
        'label' => t('Add Accordion'),
        'image' => $path . '/accordion.png',
      ),
    );
  }

}
