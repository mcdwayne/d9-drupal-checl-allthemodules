<?php

namespace Drupal\past_testhidden\Form;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Displays a form with just an submit button.
 */
class FormEmptySubmitArray extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'past_testhidden_form_empty_submit_array';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    drupal_set_message('form handler called by past_testhidden_form_empty_submit_array');
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Submit',
      '#submit' => [],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message(new FormattableMarkup('global submit handler called by @form_id', ['@form_id' => $form['#form_id']]));
  }

}
