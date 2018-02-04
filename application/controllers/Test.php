<?php

class Test extends CI_Controller {

  protected $test_db;

  public function __construct()
  {
    parent::__construct();

    $this->load->library('unit_test');
    $this->test_db = $this->load->database('test', TRUE);
  }

  public function test1()
  {
    $test = 1 + 1;

    $expected_result = 2;

    $test_name = 'Adds one plus one';

    $this->unit->run($test, $expected_result, $test_name);

    echo $this->unit->report();
  }

  public function test_user1()
  {
    $this->load->model('user_model');
    $this->test_db->empty_table('users');
    $this->_add_user();

    // New user nick must be the fresh
    $test_name = 'Modify old user nick if new user has the same';

    $user = $this->user_model->find_by('uid', '123456');

    $this->unit->run($user->nickname, 'test_nick', $test_name);

    echo $this->unit->report();
  }

  private function _add_user()
  {
     $test_user = [
      'uid' => '123456',
      'nickname' => 'test_nick',
      'email' => 'test@example.com',
      'name' => 'Test Name',
      'avatar' => 'http://via.placeholder.com/50x50',
      'token' => 'token',
      'token_secret' => 'secret'
    ];

    $this->test_db->insert('users', $test_user);
  }
}
