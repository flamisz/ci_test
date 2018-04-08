<?php

class Authenticate
{

  protected $current_user;
  protected $CI;

  function __construct()
  {
    $this->CI =& get_instance();
    $this->CI->load->model('user_model');
    $this->CI->load->helper('cookie');
  }

  public function login($user)
  {
    $this->CI->session->user_id = $user->id;
  }

  public function remember($user)
  {
    $remember_token = $this->CI->user_model->remember($user);
    set_coded_permanent_cookie('user_id', $user->id);
    set_permanent_cookie('remember_token', $remember_token);
  }

  public function current_user()
  {
    if ($user_id = $this->CI->session->user_id)
    {
      $this->current_user = $this->current_user ?: $this->CI->user_model->find_by('id', $user_id);
    }
    elseif ($user_id = get_coded_cookie('user_id'))
    {
      $user = $this->CI->user_model->find_by('id', $user_id);
      if ($user && $this->CI->user_model->is_authenticated($user, get_cookie('remember_token')))
      {
        $this->login($user);
        $this->current_user = $user;
      }
    }

    return $this->current_user;
  }

  public function is_logged_in()
  {
    return ! ! $this->current_user();
  }

  public function logout()
  {
    $this->forget($this->current_user());
    unset($_SESSION['user_id']);
    $this->current_user = NULL;
  }

  public function forget($user)
  {
    $this->CI->user_model->forget($user);
    delete_cookie('user_id');
    delete_cookie('remember_token');
  }
}
