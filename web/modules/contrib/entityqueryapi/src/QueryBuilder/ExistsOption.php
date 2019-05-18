<?php

namespace Drupal\entityqueryapi\QueryBuilder;

class ExistsOption implements QueryOptionInterface {

  /**
   * A unique key.
   *
   * @var string
   */
  protected $id;

  /**
   * A unique key representing the intended parent of this option.
   *
   * @var string|NULL
   */
  protected $parentId;

  /**
   * Boolean representing whether the field should or shouldn't exist.
   *
   * @var bool
   */
  protected $exists;

  /**
   * String representation of the entity field in to be checked.
   *
   * @var string
   */
  protected $field;

  /**
   * The langcode of the field to check.
   *
   * @var string
   */
  protected $langcode;

  public function __construct($id, $field, $exists, $langcode = NULL, $parent_id = NULL) {
    $this->id = $id;
    $this->field = $field;
    $this->exists = $exists;
    $this->langcode = $langcode;
    $this->parentId = $parent_id;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function apply($query) {
    if ($this->exists) {
      return $query->exists($this->field, $this->langcode);
    }
    else {
      return $query->notExists($this->field, $this->langcode);
    }
  }

  /**
   * Returns the id of this option's parent.
   *
   * @return string|NULL
   *  Either the id of its parent or NULL.
   */
  public function parentId() {
    return $this->parentId;
  }

}
