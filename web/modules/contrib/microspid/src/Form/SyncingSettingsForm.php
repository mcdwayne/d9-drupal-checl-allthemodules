<?php

namespace Drupal\microspid\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form builder for the microspid local settings form.
 */
class SyncingSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'microspid_syncing_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['microspid.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('microspid.settings');

    $form['user_info'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('User info and syncing'),
      '#collapsible' => FALSE,
    );

    $form['user_info']['username_fiscalnumber'] = array(
        '#type' => 'checkbox', //checkbox
        '#title' => $this->t('Fiscal number as username'),
        '#default_value' => $config->get('username_fiscalnumber', FALSE),
        '#description' => $this->t('Checked if fiscal number is used as username.'),
    );

	$my_fields = array('' => $this->t('no field'));
	$field_map = \Drupal::entityManager()->getFieldMap();
	$fields = $field_map['user'];
	//echo '<pre>';
	//print_r ($user_field_map);
	foreach ($fields as $name => $field) {
		if (isset($field['bundles']['user']) && !empty($field['bundles']['user'])) {
			if (isset($field['type']) && ($field['type']=='string'))
				if (strncmp('field_', $name, 6) == 0)
					$my_fields[$name] = $name;
		}
	}

    $form['user_info']['firstname'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for name'),
		'#options' => $my_fields,
        '#default_value' => $config->get('firstname', 'field_nome'),
        '#description' => $this->t('Select field for name'),
    );
	
    $form['user_info']['lastname'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for family name'),
		'#options' => $my_fields,
        '#default_value' => $config->get('lastname', 'field_cognome'),
        '#description' => $this->t('Select field for family name'),
    );
	
    $form['user_info']['place'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for birth place'),
		'#options' => $my_fields,
        '#default_value' => $config->get('place', 'field_luogo_di_nascita'),
        '#description' => $this->t('Select field for birth place'),
    );
	
    $form['user_info']['prov'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for province'),
		'#options' => $my_fields,
        '#default_value' => $config->get('prov', 'field_provincia_di'),
        '#description' => $this->t('Select field for province'),
    );
	
	$my_fields = array(''=>$this->t('no field'));
	$field_map = \Drupal::entityManager()->getFieldMap();
	$fields = $field_map['user'];
	foreach ($fields as $name => $field) {
		if (isset($field['bundles']['user']) && !empty($field['bundles']['user'])) {
//			if (isset($field['type']) && ($field['type']=='string'))
				if (strncmp('field_', $name, 6) == 0)
					$my_fields[$name] = $name;
		}
	}

    $form['user_info']['date'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for birth date'),
		'#options' => $my_fields,
        '#default_value' => $config->get('date', 'field_data_di_nascita'),
        '#description' => $this->t('Select field for birth date'),
    );
	
	$my_fields = array(''=>$this->t('no field'));
	$field_map = \Drupal::entityManager()->getFieldMap();
	$fields = $field_map['user'];
	foreach ($fields as $name => $field) {
		if (isset($field['bundles']['user']) && !empty($field['bundles']['user'])) {
			if (isset($field['type']) && ($field['type']=='string' || $field['type']=='codice_fiscale_basic'))
				if (strncmp('field_', $name, 6) == 0)
					$my_fields[$name] = $name;
		}
	}

    $form['user_info']['cf'] = array(
        '#type' => 'select', 
        '#title' => $this->t('Field for fiscal number'),
		'#options' => $my_fields,
        '#default_value' => $config->get('cf', 'field_codice_fiscale'),
        '#description' => $this->t('Select field for fiscal number'),
    );

    $form['user_info']['mail_attr'] = [
      '#type' => 'textfield',
      '#title' => $this->t('MicroSPiD attribute to be used as email address for the user'),
      '#default_value' => $config->get('mail_attr'),
      '#description' => $this->t('Example: <i>mail</i><br />If the user attribute is multivalued, the first value will be used.'),
    ];
    $form['user_info']['mail_attr_sync'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Synchronize email address on every login'),
      '#default_value' => $config->get('sync.mail'),
      '#description' => $this->t('Check if email address should be synchronized every time a user logs in.'),
      '#required' => FALSE,
    ];
    $form['user_info']['role_population'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Automatic role population from MicroSPiD attributes'),
      '#default_value' => $config->get('role.population'),
      '#description' => $this->t('A pipe separated list of rules. Each rule consists of a Drupal role id, a SimpleSAML attribute name, an operation and a value to match. <i>e.g. role_id1:attribute_name,operation,value|role_id2:attribute_name2,operation,value... etc</i><br /><br />Each operation may be either "@", "@=" or "~=". <ul><li>"=" requires the value exactly matches the attribute;</li><li>"@=" requires the portion after a "@" in the attribute to match the value;</li><li>"~=" allows the value to match any part of any element in the attribute array.</li></ul>For instance:<br /><i>staff:eduPersonPrincipalName,@=,uninett.no;affiliation,=,employee|admin:mail,=,andreas@uninett.no</i><br />would ensure any user with an eduPersonPrinciplaName SAML attribute matching .*@uninett.no would be assigned a staff role and the user with the mail attribute exactly matching andreas@uninett.no would assume the admin role.'),

      // A '=' requires the $value exactly matches the $attribute, A '@='
      // requires the portion after a '@' in the $attribute to match
      // theuninett.no $value and a '~=' allows the value to match any part of
      // any element in the $attribute array.
      // The full role map string, when mapped to the variables below, presents
      // itself thus:
      // $role_id:$key,$op,$value;$key,$op,$value|$role_id:$key,$op,$value etc.
    ];
    $form['user_info']['role_eval_every_time'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reevaluate roles every time the user logs in'),
      '#default_value' => $config->get('role.eval_every_time'),
      '#description' => $this->t('NOTE: This means users could lose any roles that have been assigned manually in Drupal.'),
    ];
    $form['user_info']['autoenablesaml'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Automatically enable SAML authentication for existing users upon successful login'),
      '#default_value' => $config->get('autoenablesaml'),
      '#description' => $this->t('Upon federated login, check if a local, pre-existing Drupal user is present that can be linked to the SAML authname (by default Drupal username is checked). If so, enable SAML authentication for this existing user.<br />WARNING: make sure there is an actual link between the SAML authname and pre-existing Drupal usernames, otherwise the Drupal user could be taken over by someone else authenticating with a SAML authname that happens to be the same.<br />NOTE: When enabled, the pre-existing user can be modified (e.g. get other username, email address, roles, ... based on SAML attributes).'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('microspid.settings');
/*
    $config->set('unique_id', $form_state->getValue('unique_id'));
    $config->set('user_name', $form_state->getValue('user_name'));
    $config->set('sync.user_name', $form_state->getValue('user_name_sync'));
*/
    $config->set('mail_attr', $form_state->getValue('mail_attr'));
    $config->set('sync.mail', $form_state->getValue('mail_attr_sync'));
    $config->set('role.population', $form_state->getValue('role_population'));
    $config->set('role.eval_every_time', $form_state->getValue('role_eval_every_time'));
    $config->set('autoenablesaml', $form_state->getValue('autoenablesaml'));

    $config->set('username_fiscalnumber', $form_state->getValue('username_fiscalnumber'));
    $config->set('firstname', $form_state->getValue('firstname'));
    $config->set('lastname', $form_state->getValue('lastname'));
    $config->set('place', $form_state->getValue('place'));
    $config->set('prov', $form_state->getValue('prov'));
    $config->set('date', $form_state->getValue('date'));
    $config->set('cf', $form_state->getValue('cf'));
	
    $config->save();
  }

}
