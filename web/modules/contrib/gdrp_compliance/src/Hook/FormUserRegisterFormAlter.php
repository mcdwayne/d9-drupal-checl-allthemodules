<?php

namespace Drupal\gdrp_compliance\Hook;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\gdrp_compliance\Controller\FormWarning;

/**
 * AjaxContactForm.
 */
class FormUserRegisterFormAlter extends ControllerBase {

  /**
   * Hook.
   */
  public static function hook(&$form, FormStateInterface $form_state, $form_id) {
    if (\Drupal::config('gdrp_compliance.settings')->get('user-register')) {
      FormWarning::addWarning($form);
    };
  }

}
