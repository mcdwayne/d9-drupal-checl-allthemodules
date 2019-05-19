<?php

namespace Drupal\sidr\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Global sidr settings form.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sidr_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'sidr.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('sidr.settings');
    $form['sidr_theme'] = [
      '#type' => 'select',
      '#title' => $this->t('Sidr theme'),
      '#description' => $this->t('If you want to style Sidr with your own CSS, choose %bare', [
        '%bare' => $this->t('Bare'),
      ]),
      '#default_value' => $config->get('sidr_theme') ?: 'bare',
      '#options' => [
        'bare' => $this->t('Bare'),
        'light' => $this->t('Light'),
        'dark' => $this->t('Dark'),
      ],
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $keys = [
      'sidr_theme',
    ];

    // Save configuration changes.
    $config = $this->config('sidr.settings');
    foreach ($keys as $key) {
      $config->set($key, $form_state->getValue($key));
    }
    $config->save();

    parent::submitForm($form, $form_state);
  }

}
