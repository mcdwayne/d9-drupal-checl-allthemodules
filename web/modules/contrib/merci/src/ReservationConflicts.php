<?php


/**
 * @file
 * Contains \Drupal\merci\ReservationConflicts.
 * Abstraction of the selection logic of an entity reference field.
 *
 * Implementations that wish to provide an implementation of this should
 * register it using CTools' plugin system.
 */

namespace Drupal\merci;

use \Drupal\merci\ReservationConflictsInterface;
use \Drupal\Core\Link;
/**
 * A null implementation of EntityReference_SelectionHandler.
 */
class ReservationConflicts implements ReservationConflictsInterface {

  protected $entity;
  protected $date_field;
  protected $item_field;
  protected $quantity_field;
  protected $validated;
  protected $parent_quantity_field;
  protected $conflicting_entities;
  protected $total_buckets_filled;
  protected $buckets;
  protected $errors;

  protected $date_column, $date_column2;

  public function setEntity(\Drupal\Core\Entity\FieldableEntityInterface $entity) {
    $this->entity = $entity;
  }

  public function getEntity() {
    return $entity;
  }

  public function setDateField($date_field) {
    $this->date_field = $date_field;
    $date_storage = $this->entity->get($this->date_field)->getFieldDefinition()->getFieldStorageDefinition();
    $date_columns = $date_storage->getColumns();
    $this->date_column  = $this->date_field . '_' . key($date_columns);
    next($date_columns);
    $this->date_column2 = $this->date_field . '_' . key($date_columns);
  }

  public function getDateField() {
    return $date_field;
  }

  public function setItemField($item_field) {
    $this->item_field = $item_field;
  }

  public function getItemField() {
    return $item_field;
  }

  public function setQuantityField($quantity_field) {
    $this->quantity_field = $quantity_field;
  }

  public function getQuantityField() {
    return $quantity_field;
  }

  public function setParentQuantityField($parent_quantity_field) {
    $this->parent_quantity_field = $parent_quantity_field;
  }

  public function getParentQuantityField() {
    return $parent_quantity_field;
  }

  public function validate() {
    if (!$this->validated) {
      $this->buckets = $this->fillBuckets();
      $this->validated = TRUE;
      $conflicts = array();
      foreach ($this->buckets as $delta => $dates) {
        foreach ($dates as $date_value => $buckets){
          if (!isset($this->total_buckets_filled[$delta])) {
            $this->total_buckets_filled[$delta] = array();
          }
          $this->total_buckets_filled[$delta][$date_value] = count($buckets);
          if (!isset($conflicts[$delta])) {
            $conflicts[$delta] = array();
          }
          $conflicts[$delta][$date_value] = array();
          foreach ($buckets as $bucket) {
            $conflicts[$delta][$date_value] = array_merge($conflicts[$delta][$date_value], $bucket);
          }
        }
      }
      $this->conflicting_entities = $conflicts;
    }
  }

  public function getErrors($delta = NULL) {
    if ($this->errors === NULL) {
      $this->validate();
      $entity = $this->entity;
      $entity_type  = $this->entity->getEntityTypeId();
      $errors = array();

      // Determine if reserving too many of the same item.

      // How many of each item are we trying to reserve?
      if ($entity->hasField($this->parent_quantity_field)) {
        $quantity_reserved = $entity->get($this->parent_quantity_field)->value;
      }
      else {
        $quantity_reserved = 1;
      }

      // How many times was the item selected?
      foreach ($entity->get($this->item_field) as $delta => $resource) {

        $item_id = $resource->target_id;

        if (empty($item_id)) {
          continue;
        }

        if (empty($item_count[$item_id])) {
          $item_count[$item_id] = 0;
        }
        $item_count[$item_id] += $quantity_reserved;

        if ($resource->entity->hasField($this->quantity_field)) {
          $quantity_reservable = $resource->entity->get($this->quantity_field)->value;
        }
        else {
          $quantity_reservable = 1;
        }

        // Did we select too many?
        if ($item_count[$item_id] > $quantity_reservable) {
          // Selected to many.
          if (!array_key_exists($delta, $errors)) {
            $errors[$delta] = array();
          }
          $parents_path = implode('][', array($this->item_field, 'und', $delta, 'target_id'));
          $errors[$delta][MERCI_ERROR_TOO_MANY] = t('@name: You have selected too many of the same item.  We only have @quantity available but you reserved @reserved.',
            array(
              '@name' => $resource->entity->label(),
              '@quantity' => $quantity_reservable,
              '@reserved' => $item_count[$item_id],
            ));
        }
      }

      $total_buckets_filled = $this->getTotalBucketsFilled();

      $total_buckets_filled = $total_buckets_filled ? $total_buckets_filled : array();

      $reservation_counter = array();

      foreach ($total_buckets_filled as $delta => $start_dates) {

        $conflict_errors = array();

        $resource = $entity->get($this->item_field)[$delta];

        if ($resource->entity->hasField($this->quantity_field)) {
          $quantity_reservable = $resource->entity->get($this->quantity_field)->value;
        }
        else {
          $quantity_reservable = 1;
        }

        $item_id = $resource->target_id;
        if (empty($reservation_counter[$item_id])) {
          $reservation_counter[$item_id] = 0;
        }
        $reservation_counter[$item_id] += $quantity_reserved;

        foreach ($this->entity->get($this->date_field) as $dates) {

          $used_buckets = $this->getTotalBucketsFilled($delta, $dates);


          // Determine if there are conflicts for this date and item.
          if ($quantity_reservable >= $used_buckets + $reservation_counter[$item_id]) {
            continue;
          }
          // Load each conflicting entity so we can show information about it to
          // the user.
          $ids = array();
          foreach ($this->getConflicts($delta, $dates) as $conflict) {
            $ids[] = $conflict->parent_id;
          }

          // Load the entities which hold the conflicting item.
          $entities = \Drupal::entityManager()->getStorage($entity_type)->loadMultiple($ids);

          $line_items = array();

          foreach ($entities as $id => $line_item) {
            $entity_uri = $line_item->toUrl();//entity_uri($entity_type, $line_item);
            $entity_label = $line_item->label();//entity_label($entity_type, $line_item);
            $line_items[] = Link::fromTextAndUrl($entity_label, $entity_uri)->toString();
          }

          $date_start = $dates->get('value')->getValue();
          // Don't show the date repeat rule in the error message.

          // @FIXME
          //$render_dates = field_view_value($entity_type, $entity->value(), $this->date_field, $dates);
          $conflict_errors[$date_start] = t('@name is already reserved by: :items for selected dates @dates',
            array(
              '@name' => $resource->entity->label(),
              ':items' => implode(', ', $line_items),
              '@dates' => render($render_dates),
            ));
        }
        if ($conflict_errors) {
          if (!array_key_exists($delta, $errors)) {
            $errors[$delta] = array();
          }
          $errors[$delta][MERCI_ERROR_CONFLICT] = $conflict_errors;
        }
      }
      $this->errors = $errors;
    }
    return $this->errors;
  }

  public function getConflicts($delta = NULL, $dates = NULL) {

    $this->validate();
    $conflicts = $this->conflicting_entities;

    if ($delta === NULL) {
      return $conflicts;
    }

    if (empty($dates)) {
      return array_key_exists($delta, $conflicts) ?
        $conflicts[$delta] : FALSE;
    }

    $date_value = $dates->get('value')->getValue();
    return (array_key_exists($delta, $conflicts) and array_key_exists($date_value, $conflicts[$delta])) ?
      $conflicts[$delta][$date_value] : FALSE;
  }

  public function getTotalBucketsFilled($delta = NULL, $dates = NULL) {

    $this->validate();
    $total_buckets_filled = $this->total_buckets_filled;

    if ($delta === NULL) {
      return $total_buckets_filled;
    }

    if (empty($dates)) {
      return array_key_exists($delta, $total_buckets_filled) ?
        $total_buckets_filled[$delta] : 0;
    }

    $date_value = $dates->get('value')->getValue();
    return (array_key_exists($delta, $total_buckets_filled) and array_key_exists($date_value, $total_buckets_filled[$delta])) ?
      $total_buckets_filled[$delta][$date_value] : 0;
  }

  /*
   * Determine if merci_line_item $entity conflicts with any other existing line_items.
   *
   * Returns array of conflicting line items.
   */

  public function conflicts($date) {
    $conflicts = array();

    $date_value = $date->get('value')->getValue();

    $query = $this->buildConflictQuery($date);

    $result = $query->execute();

    $line_item_entities = \Drupal::entityTypeManager()->getStorage($this->entity->getEntityTypeId())->loadMultiple($result);

    foreach ($line_item_entities as $entity) {
      $dates = $entity->{$this->date_field}->getValue();
      $dates = reset($dates);
      foreach ($entity->{$this->item_field} as $item) {
        $target_id = $item->{'target_id'};
        $record = new \stdClass();
        $record->item_id = $target_id;
        $record->parent_id = $entity->id();

        if ($entity->hasField($this->parent_quantity_field)) {
          $record->quantity = (int)$entity->get($this->parent_quantity_field)->value;
        }
        else {
          $record->quantity = 1;
        }
        $record->{$this->date_column} = $dates['value'];
        $record->{$this->date_column2} = $dates['end_value'];
        if (!isset($conflicts[$target_id])) {
          $conflicts[$target_id] = array();
        }
        if (!isset($conflicts[$target_id][$date_value])) {
          $conflicts[$target_id][$date_value] = array();
        }
        $conflicts[$target_id][$date_value][] = $record;
      }
    }

    $return = array();

    $items = $this->entity->get($this->item_field);
    foreach ($items as $delta => $item) {
      if (isset($conflicts[$item->target_id])) {
        $return[$delta] = $conflicts[$item->target_id];
      }
    }
    return $return;
  }

  public function conflictingEntities($date, $item = NULL) {
    $date_value = $date->get('value')->getValue();

    $query = $this->buildConflictQuery($date, $item);

    $query->addTag('debug');
    $result = $query->execute();

    $line_item_entities = \Drupal::entityTypeManager()->getStorage($this->entity->getEntityTypeId())->loadMultiple($result);

    return $line_item_entities;
  }

  public function buildConflictQuery($date, $item = NULL) {

    $exclude_id   = $this->entity->id();
    $entity_type  = $this->entity->getEntityTypeId();

    $items = array();

    if ($item) {
      $items[] = $item->target_id;
    }
    else {
      foreach ($this->entity->get($this->item_field) as $delta => $item) {
        $items[] = $item->target_id;
      }
    }


    // Build the query.
    // Entity type is the entity holding the date and item fields.
    $query = \Drupal::entityQuery($entity_type);

    if (count($items) == 1) {
      $query->condition($this->item_field, reset($items));
    } else {
      $query->condition($this->item_field, $items, 'IN');
    }

    // Ignore myself.
    if ($exclude_id) {
      $entity_type_id_key = $this->entity->getEntityType()->getKey('id');
      $query->condition($entity_type_id_key, $exclude_id, '!=');
    }

    $dates = array(
      'value' => $date->get('value')->getValue(),
      'end_value' => $date->get('end_value')->getValue()
    );

      //  start falls within another reservation.
      //                     |-------------this-------------|
      //            |-------------conflict-------------------------|
      //            OR
      //                     |-------------this-------------------------------|
      //            |-------------conflict-------------------------|
    $and1 = $query->andConditionGroup()
      ->condition($this->date_field . '.value', $dates['value'], '<=')
      ->condition($this->date_field . '.end_value', $dates['value'], '>=');
      //  end falls within another reservation.
      //                     |-------------this-------------------------------|
      //                                   |-------------conflict-------------------------|
    $and2 = $query->andConditionGroup()
      ->condition($this->date_field . '.value', $dates['end_value'], '<=')
      ->condition($this->date_field . '.end_value', $dates['end_value'], '>=');
      //  start before another reservation.
      //  end after another reservation.
      //                     |-------------------------this-------------------------------|
      //                            |----------------conflict------------------|
    $and3 = $query->andConditionGroup()
      ->condition($this->date_field . '.value', $dates['value'], '>')
      ->condition($this->date_field . '.end_value', $dates['end_value'], '<');
    $or = $query->orConditionGroup()
      ->condition($and1)
      ->condition($and2)
      ->condition($and3);
    $query->condition($or);

    $query->sort($this->date_field . '.value');

    // Add a generic entity access tag to the query.
    $query->addTag('merci_resource');
    $query->addMetaData('merci_reservable_handler', $this);

    return $query;
  }

  public function reservations($dates, $exclude_id) {
    $bestfit = $this->bestFit($dates);
    $reservations = array();
    foreach ($bestfit as $enity_id => $reservation) {
      $reservations[] = $entity_id;
    }
    return $reservations;
  }

  public function fillBuckets() {
    $conflicts = array();

    $dates = $this->entity->get($this->date_field);
    foreach ($dates as $date) {
      $date_value = $date->get('value')->getValue();
      $result = $this->bestFit($date);
      // Result is array indexed by $delta of filled buckets.
      foreach ($result as $delta => $buckets) {
        if (!isset($conflicts[$delta])) {
          $conflicts[$delta] = array();
        }
        $conflicts[$delta][$date_value] = $buckets;

      }
    }
    return $conflicts;
  }

  /*
   * Perform first-fit algorhtym on reservations into buckets.
   *
   * Return array indexed by item delta of array of filled buckets.
   */
  public function bestFit($dates) {

    $entity = $this->entity;
    $best_fit = array();


    $parent_conflicts = $this->conflicts($dates);

    $date_value = $dates->get('value')->getValue();

    foreach ($entity->get($this->item_field) as $delta => $item) {

      // No need to sort into buckets if there is nothing to sort into buckets.
      if (!array_key_exists($delta, $parent_conflicts) or !array_key_exists($date_value, $parent_conflicts[$delta])) {
        continue;
      }

      if ($item->entity->hasField($this->quantity_field)) {
        $quantity = $item->entity->get($this->quantity_field)->value;
      }
      else {
        $quantity = 1;
      }

      // Split reservations based on quantity.
      $reservations = array();

      foreach($parent_conflicts[$delta][$date_value] as $reservation) {
        for ($i = 0; $i < $reservation->quantity; $i++) {
          $reservations[] = $reservation;
        }
      }

      // Determine how many bucket items are needed for this time period.
      // Need to sort like this:
      //            .... time ....
      // item1  x x a a a x x x x x f x e e e x x x x x
      // item2  x x x d d d d d d x x x x c c c x x x x
      // item3  x x b b b b b b b b b b b b b x x x x x
      // etc ......
      //
      //      // Order by lenght of reservation descending.
      //      // Do first-fit algorythm.

      // Sort by length of reservation.
      uasort($reservations, array($this, "merci_bucket_cmp_length"));

      $buckets = array();
      // First-fit algorythm.
      foreach ($reservations as $test_reservation) {

        // Go through each bucket item to look for a available slot for this reservation.
        //
        // Find a bucket to use for this reservation.
        for ($i = 0; $i < $quantity; $i++) {

          $fits = TRUE;
          // Bucket already has other reservations we need to check against for a fit.
          if (array_key_exists($i, $buckets)) {
            foreach ($buckets[$i] as $reservation) {
              if ($this->merci_bucket_intersects($reservation, $test_reservation)) {
                //Conflict so skip saving the reservation to this slot and try to use the next bucket item.
                $fits = FALSE;
                break;
              }
            }
          }

          // We've found a slot so test the next reservation.
          if ($fits) {
            if (array_key_exists($i, $buckets)) {
              $buckets[$i] = array();
            }
            $buckets[$i][] = $test_reservation;
            break;
          }

        }
      }
      if (count($buckets)) {
        $best_fit[$delta] = $buckets;
      }
    }
    return $best_fit;
  }

/*
 * |----------------------|        range 1
 * |--->                           range 2 overlap
 *  |--->                          range 2 overlap
 *                        |--->    range 2 overlap
 *                         |--->   range 2 no overlap
 */
  private function merci_bucket_intersects($r1, $r2) {
    $value = $this->date_column;
    $end_value = $this->date_column2;
    /*
     * Make sure r1 start date is before r2 start date.
     */
    if (date_create($r1->{$value}) > date_create($r2->{$value})) {
      $temp = $r1;
      $r1 = $r2;
      $r2 = $temp;
    }

    if (date_create($r2->{$value}) <= date_create($r1->{$end_value})) {
      return true;
    }
    return false;

  }

  private function merci_bucket_cmp_length($a, $b) {
    $value = $this->date_column;
    $end_value = $this->date_column2;
    $len_a = date_format(date_create($a->{$end_value}),'U') - date_format(date_create($a->{$value}), 'U');
    $len_b = date_format(date_create($b->{$end_value}),'U') - date_format(date_create($b->{$value}), 'U');
    if ($len_a == $len_b) {
      return 0;
    }
    return ($len_a < $len_b) ? 1 : -1;
  }

}
