<?php

/**
 * @file
 * Contains \Drupal\views_system\Plugin\views\field\ViewsSystemRegionsHidden.
 */


namespace Drupal\views_system\Plugin\views\field;

use Drupal\views\Plugin\views\field\PrerenderList;


/**
 * Field handler to display all hidden regions of a theme.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("views_system_regions_hidden")
 */
class ViewsSystemRegionsHidden extends PrerenderList {

  public function preRender(&$values) {
    $this->items = array();

    foreach ($values as $result) {

      $field = $this->getValue($result);
      if (!empty($field) && !isset($this->items[$field])) {

        foreach (unserialize($field) as $name) {

          $this->items[$field][$name]['name'] = $name;
        }
      }
    }
  }

  function render_item($count, $item) {
    return $item['name'];
  }
}
