<?php

namespace Drupal\uc_order\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\GroupByNumeric;

/**
 * Filter handler that handles fields generated by table.fieldname * table.qty.
 *
 * This extends views_handler_filter_group_by_numeric because the field is an
 * alias for a formula. WHERE clauses can't use aliases. Extending it this way
 * uses a HAVING clause instead, which does work.
 *
 * This filter handler is appropriate for any numeric formula that ends up
 * in the query with an alias like "table_field".
 *
 * @ViewsFilter("uc_order_total")
 *
 * @ingroup views_filter_handlers
 */
class Total extends GroupByNumeric {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensure_my_table();
    $field = $this->table . '_' . $this->field;

    $info = $this->operators();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($field);
    }
  }

}
