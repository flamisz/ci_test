<?php

class Test extends CI_Controller {

  public function __construct()
  {
    parent::__construct();

    $this->load->library('unit_test');
    $this->db->close();
    $this->load->database('test');
  }

  public function test1()
  {
    $test = 1 + 1;

    $expected_result = 2;

    $test_name = 'Adds one plus one';

    $this->unit->run($test, $expected_result, $test_name);

    echo $this->unit->report();
  }

  public function user_login()
  {
    $this->load->model('user_model');
    $this->db->empty_table('users');
    $this->_add_user();


    $twitter_user = [
      'uid' => '654321',
      'nickname' => 'test_nick',
      'email' => 'new@example.com',
      'name' => 'New Name',
      'avatar' => 'http://via.placeholder.com/50x50',
      'token' => 'token',
      'token_secret' => 'secret'
    ];

    $this->user_model->from_oauth($twitter_user);

    $user_old = $this->user_model->find_by('uid', '123456');
    $user_new = $this->user_model->find_by('uid', '654321');

    $test_name = 'Old user get modified nickname';
    $old_nick = 'test_nick-' . $user_old->id;
    $this->unit->run($user_old->nickname, $old_nick, $test_name);

    $test_name = 'New user get the nickname';
    $this->unit->run($user_new->nickname, 'test_nick', $test_name);

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

    $this->db->insert('users', $test_user);
  }
}
