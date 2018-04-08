<?php

class Welcome extends CI_Controller {

  public function __construct()
  {
    parent::__construct();

  }

  public function index()
  {
    // $this->output->enable_profiler(TRUE);
    $data['is_logged_in'] = $this->authenticate->is_logged_in();
    $data['user'] = $this->authenticate->current_user();
    $this->load->view('welcome_message', $data);
  }
}
