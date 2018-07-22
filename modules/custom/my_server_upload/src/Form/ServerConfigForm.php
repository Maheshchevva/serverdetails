<?php

namespace Drupal\my_server_upload\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a config blocks form.
 */
class ServerConfigForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'server_config_form';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'my_server_upload.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
   $config = $this->config('my_server_upload.settings'); 
   $form['enable_server_config'] = array(
     '#type' => 'checkbox',
     '#title' => $this->t('Check to enable server root username and password'),
     '#default_value' => $config->get('enable_server_config'),	 
   );
   return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	$config = \Drupal::service('config.factory')->getEditable('my_server_upload.settings');
    $config->set('enable_server_config', $form_state->getValue('enable_server_config'));
    $config->save();
    parent::submitForm($form, $form_state);
  }
}