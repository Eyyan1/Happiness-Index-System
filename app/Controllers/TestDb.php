<?php namespace App\Controllers;
class TestDb extends BaseController {
  public function index() {
    $db = \Config\Database::connect();
    $data = $db->query('SELECT * FROM userss WHERE ROWNUM < 5')->getResult();
    echo '<pre>'.print_r($data, true).'</pre>';
  }
}
