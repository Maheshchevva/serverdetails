<?php

namespace Drupal\my_server_upload\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Database\Database;
use Drupal\Core\Database\DatabaseException;

class MyServerUploadController extends ControllerBase {
  public function FormDisplay() {
    return array(
      '#title' => 'My Server Page',
      '#markup' => '',
    );
  }
  
  public function ServerDetailsList() {
    $header = array(
        array('data' => t('Server Name'), 'field' => 'server_name', 'sort' => 'desc'),
        array('data' => t('Server Type'), 'field' => 'server_type'),
        array('data' => t('IP Address'), 'field' => 'ip_address'),
        array('data' => t('Fqdm'), 'field' => 'fqdm'),
    );
      $connection = Database::getConnection();
      $query = $connection->select('server_information', 'si');
      $query->fields('si', array('server_name', 'server_type', 'ip_address', 'fqdm'));
	  $table_sort = $query->extend('Drupal\Core\Database\Query\TableSortExtender')->orderByHeader($header);
      $pager = $table_sort->extend('Drupal\Core\Database\Query\PagerSelectExtender')->limit(10);
      $result = $pager->execute();

      foreach($result as $row) {
        $rows[] = array('data' => (array) $row);
      }
      $build = array(
        '#markup' => t('List of server details')
      );
      $build['server_table'] = array(
        '#theme' => 'table', '#header' => $header,
          '#rows' => $rows
      );
     $build['pager'] = array(
       '#type' => 'pager'
     );
      return $build;	 
  }
}