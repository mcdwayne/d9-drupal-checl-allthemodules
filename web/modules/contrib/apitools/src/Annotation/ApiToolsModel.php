<?php

namespace Drupal\apitools\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a ApiToolsModel item annotation object.
 *
 * @see \Drupal\apitools\ModelManager
 * @see plugin_api
 *
 * @Annotation
 */
class ApiToolsModel extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

}
