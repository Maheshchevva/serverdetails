<?php

namespace Drupal\my_server_upload\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\DatabaseException;
use Drupal\Core\Logger\LogMessageParserInterface;


class ServerDetailsForm extends FormBase {

  public function getFormId() {
    return 'server_details_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_user = \Drupal::currentUser();
    $uid = $current_user->id();
	$email = $uid ? $current_user->getEmail() : '';
	$name = $uid ? $current_user->getAccountName() : '';
    $roles = $current_user->getRoles();
	
	if($uid) {
      $connection = Database::getConnection();
      $server_info = $connection->select('server_information', 'si')
      ->fields('si')
      ->condition('uid', $uid)
      ->execute()
      ->fetchAll();
      $server_maintainer = $connection->select('server_maintainer', 'sm')
      ->fields('sm')
      ->condition('uid', $uid)
      ->execute()
      ->fetchAll();
	  if($server_maintainer[0]->id) {
        drupal_set_message("Server edit form");  
      }
    }

    if($current_user->hasPermission('access my server')) {
      $form = [];
      $form['server_information'] = array(
        '#type' => 'fieldset',
        '#title' => $this->t('Server Information'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
	    '#prefix' => '<div class="col-sm-6">',
	    '#suffix' => '</div>',
      );
      $form['server_information']['server_name'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Server Name'),
        '#required' => TRUE,
		'#default_value' => $server_info[0]->server_name ? $server_info[0]->server_name : '',
        '#description' => $this->t('Enter the server name'),
      );

      $serverSettings = \Drupal::config('my_server_upload.settings');
      $val = $serverSettings->get('enable_server_config');
      if($val == 1) {
        $form['server_information']['server_username'] = array(
          '#type' => 'textfield',
          '#title' => $this->t('Server root username'),
          '#required' => TRUE,
		  '#default_value' => $server_info[0]->server_username ? $server_info[0]->server_username : '',
          '#description' => $this->t('Enter the server root username'),
        );
        $form['server_information']['server_password'] = array(
          '#type' => 'password',
          '#title' => $this->t('Server root password'),
          '#required' => TRUE,
		  '#default_value' => $server_info[0]->server_password ? $server_info[0]->server_password : '',
          '#description' => $this->t('Enter the server root password'),
        );
      }

      $form['server_information']['server_type'] = array(
        '#type' => 'select',
        '#title' => $this->t('Server Type'),
        '#options' => array('Windows' => 'Windows', 'Linux' => 'Linux', 'Other' => 'Other'),
		'#default_value' => $server_info[0]->server_type ? $server_info[0]->server_type : '', 
        '#description' => $this->t('Select server type'),
      );
      $form['server_information']['windows_version'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Windows version'),
        '#states' => array(
        'visible' => array(
          ':input[name="server_type"]' => array('value' => 'Windows')
        ),
        'required' => array(
          ':input[name="server_type"]' => array('value' => 'Windows')
        ),
        ),
		'#default_value' => $server_info[0]->windows_version ? $server_info[0]->windows_version : '', 
        '#description' => $this->t('Enter windows version'),
      );
      $form['server_information']['linux_version'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Linux version'),
        '#states' => array(
        'visible' => array(
          ':input[name="server_type"]' => array('value' => 'Linux')
        ),
        'required' => array(
          ':input[name="server_type"]' => array('value' => 'Linux')
        ),
        ),
		'#default_value' => $server_info[0]->linux_version ? $server_info[0]->linux_version : '', 
        '#description' => $this->t('Enter linux version'),
      );
      $form['server_information']['specify_other'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Server Type Other'),
        '#states' => array(
        'visible' => array(
          ':input[name="server_type"]' => array('value' => 'Other')
        ),
        'required' => array(
          ':input[name="server_type"]' => array('value' => 'Other')
        ),
        ),
		'#default_value' => $server_info[0]->server_other_flag ? $server_info[0]->server_type : '', 
        '#description' => $this->t('Select server type if other than Windows, Linux'),
      );
      $form['server_information']['ip_address'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('IP Address'),
        '#description' => $this->t('Enter IP Address'),
        '#required' => TRUE,
		'#default_value' => $server_info[0]->ip_address ? $server_info[0]->ip_address : '', 
      );
      $form['server_information']['fqdm'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('FQDM'),
        '#description' => $this->t('Enter FQDM'),
		'#default_value' => $server_info[0]->fqdm ? $server_info[0]->fqdm : '', 
      );

      $form['server_maintainer'] = array(
        '#type' => 'fieldset',
        '#title' => $this->t('Server Maintainer'),
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,
        '#prefix' => '<div class="col-sm-6">',
        '#suffix' => '</div>',	  
      );
      $form['server_maintainer']['server_admin'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Server Admin'),
        '#description' => $this->t('Enter Server Admin'),
		'#default_value' => $server_maintainer[0]->server_admin ? $server_maintainer[0]->server_admin : $name,
        '#required' => TRUE,
      );
      $form['server_maintainer']['server_admin_email'] = array(
        '#type' => 'email',
        '#title' => $this->t('Server Admin Mail'),
        '#description' => $this->t('Enter Server admin mail'),
        '#required' => TRUE,
		'#default_value' => $server_maintainer[0]->server_admin_email ? $server_maintainer[0]->server_admin_email : $email,
        '#size' => 60,
      );
      $form['server_maintainer']['admin_since'] = array(
        '#type' => 'date',
        '#title' => $this->t('Admin Sine'),
        '#date_format' => 'Y-m-d',
		'#default_value' => $server_maintainer[0]->server_admin_since ? date('Y-m-d', $server_maintainer[0]->server_admin_since) : '',
        '#required' => TRUE,
      );
      if(in_array("admin_user", $roles)) {
        $form['server_maintainer']['server_notes'] = array(
          '#type' => 'textfield',
          '#title' => $this->t('Server Notes'),
          '#description' => $this->t('Enter Server notes'),
		  '#default_value' => $server_maintainer[0]->server_notes ? $server_maintainer[0]->server_notes : '',
        );
      }

	  $form['submit'] = array(
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
        '#attributes' => array(
          'class' => array('server-details-form-submit'),
        ),
      );
      return $form;
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $vals = $form_state->getValues();
    $flag = ($vals['server_type'] == 'Other' ) ? 1 : 0;
    $vals['server_type'] = ($vals['server_type'] == 'Other' ) ? $vals['specify_other'] : $vals['server_type'];

    $id = \Drupal::currentUser()->id();
    if($id) {
      try {
        $connection->merge('server_information')
		  ->key(['uid' => $id])
          ->fields([
          'server_name' => $vals['server_name'],
		  'server_username' => $vals['server_username'],
		  'server_password' => $vals['server_password'],
          'server_type' => $vals['server_type'],
		  'windows_version' => $vals['windows_version'],
		  'linux_version' => $vals['linux_version'],
          'server_other_flag' => $flag,
          'ip_address' => $vals['ip_address'],
          'fqdm' => $vals['fqdm'],
        ])
        ->execute();
        $connection->merge('server_maintainer')
		->key(['uid' => $id])
        ->fields([
          'server_admin' => $vals['server_admin'],
          'server_name' => $vals['server_name'],
          'server_admin_mail' => $vals['server_admin_email'],
          'server_admin_since' => strtotime($vals['admin_since']),
          'server_notes' => $vals['server_notes'],
		  'updated' => time(),
        ])
        ->execute();
      }
      catch (\Exception $e) {
        throw $e;
      }
	}
	else {
      try {
        $connection->insert('server_information')
          ->fields([
          'server_name' => $vals['server_name'],
		  'server_username' => $vals['server_username'],
		  'server_password' => $vals['server_password'],
          'server_type' => $vals['server_type'],
		  'windows_version' => $vals['windows_version'],
		  'linux_version' => $vals['linux_version'],
          'server_other_flag' => $flag,
          'ip_address' => $vals['ip_address'],
          'fqdm' => $vals['fqdm'],
        ])
        ->execute();
        $connection->insert('server_maintainer')
        ->fields([
          'server_admin' => $vals['server_admin'],
          'server_name' => $vals['server_name'],
          'server_admin_mail' => $vals['server_admin_email'],
          'server_admin_since' => strtotime($vals['admin_since']),
          'server_notes' => $vals['server_notes'],
		  'updated' => time(),
        ])
        ->execute();
      }
      catch (\Exception $e) {
        throw $e;
      }
    }
	drupal_set_message("Server Upload Form Submitted");
	$form_state->setRedirect('<front>');
  }

}