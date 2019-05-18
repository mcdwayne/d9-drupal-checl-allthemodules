<?php

namespace Drupal\homebox\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Provides a form for deleting Homebox Layout entities.
 *
 * @ingroup homebox
 */
class HomeboxLayoutDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.homebox_layout.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entity->delete();

    $this->messenger()->addMessage(
      $this->t('content @type: deleted @label.',
        [
          '@type' => $this->entity->bundle(),
          '@label' => $this->entity->label(),
        ]
      )
    );

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    // This throws exception on submit.
  }

}
