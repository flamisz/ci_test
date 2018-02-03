<?php

use League\OAuth1\Client\Server\Twitter;

class Auth extends CI_Controller {

  private $server;

  public function __construct()
  {
    parent::__construct();

    $this->load->model('user_model');
    $this->load->helper(['url', 'cookie']);
    $this->lang->load('error_messages_lang');

    $this->server = new Twitter([
      'identifier' => 'Qfflyt0FL025wLNKbf2o1AeSd',
      'secret' => 'LLvbZM6AKXsGvEohHoL37X54sdWnfb11sU8n0vhcSUgZ9i5A8E',
      'callback_uri' => "http://ci.test/auth/callback",
    ]);
  }

  public function login()
  {
    $temporaryCredentials = $this->server->getTemporaryCredentials();
    $this->session->temporary_credentials = serialize($temporaryCredentials);
    $this->server->authorize($temporaryCredentials);
  }

  public function callback()
  {
    if (isset($_GET['denied']))
    {
      $this->session->set_flashdata('flash', $this->lang->line('error_twitter_auth'));
      redirect('welcome/index');
    }

    $temporaryCredentials = unserialize($this->session->temporary_credentials);
    $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
    unset($_SESSION['temporary_credentials']);

    $twitter_user = $this->server->getUserDetails($tokenCredentials);

    $twitter_user = [
      'uid' => $twitter_user->uid,
      'nickname' => $twitter_user->nickname,
      'email' => $twitter_user->email,
      'name' => $twitter_user->name,
      'avatar' => $twitter_user->imageUrl,
      'token' => $tokenCredentials->getIdentifier(),
      'token_secret' => $tokenCredentials->getSecret()
    ];

    $user = $this->user_model->from_oauth($twitter_user);
    var_dump($user);
    die();
    $this->_login($user);
    $this->_remember($user);

    redirect('welcome/index');
  }

  private function _login($user)
  {
    $this->session->user_id = $user->id;
  }

  private function _remember($user)
  {
    // user.remember
    // cookies.permanent.signed[:user_id] = user.id
    // cookies.permanent[:remember_token] = user.remember_token
    $this->user->remember($user);
    set_cookie($name = 'user_id', $value = $user->id, $expire = 60 * 60 * 24 * 365 * 10);
    set_cookie($name = 'remember_token', $value = $user->remember_token, $expire = 60 * 60 * 24 * 365 * 10);
  }
}
