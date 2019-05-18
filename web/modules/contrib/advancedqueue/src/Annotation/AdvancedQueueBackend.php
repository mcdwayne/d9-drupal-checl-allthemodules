<?php

namespace Drupal\advancedqueue\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines an advanced queue backend.
 *
 * Plugin Namespace: Plugin\AdvancedQueue\Backend.
 *
 * @Annotation
 */
class AdvancedQueueBackend extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the plugin.
   *
   * @ingroup plugin_translatable
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;

}
