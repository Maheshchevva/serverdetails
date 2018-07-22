<?php

namespace Drupal\my_server_upload\Plugin\Block;

use Drupal\Core\Block\BlockBase;
/**
 * Provides a 'MyServerUpload' block.
 *
 * @Block(
 *  id = "my_server_upload",
 *  admin_label = @Translation("My Server Upload Block"),
 * )
 */
class MyServerUpload extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\my_server_upload\Form\ServerDetailsForm');
    return $form;
  }
}