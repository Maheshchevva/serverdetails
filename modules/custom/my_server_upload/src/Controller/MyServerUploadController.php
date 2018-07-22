<?php

namespace Drupal\my_server_upload\Controller;

use Drupal\Core\Controller\ControllerBase;

class MyServerUploadController extends ControllerBase {
  public function FormDisplay() {
    return array(
      '#title' => 'My Server Page',
      '#markup' => '',
    );
  }	
}