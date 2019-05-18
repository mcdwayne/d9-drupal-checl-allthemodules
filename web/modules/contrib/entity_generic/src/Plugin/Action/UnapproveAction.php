<?php

namespace Drupal\entity_generic\Plugin\Action;

use Drupal\Core\Action\ActionBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\entity_generic\Entity\GenericInterface;

/**
 * @Action(
 *   id = "entity_generic:unapprove_action",
 *   label = @Translation("Unapprove entity"),
 *   deriver = "Drupal\entity_generic\Plugin\Action\Derivative\UnapproveActionDeriver",
 * )
 */
class UnapproveAction extends ActionBase {

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL) {
    $entity->setApproved(GenericInterface::ENTITY_GENERIC_UNAPPROVED);
    $entity->save();
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = $object->access('update', $account, TRUE);

    return $return_as_object ? $result : $result->isAllowed();
  }

}
