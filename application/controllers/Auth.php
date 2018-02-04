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
    $this->config->load('secret', FALSE, TRUE);

    $this->server = new Twitter([
      'identifier' => $this->config->item('twitter_api_key'),
      'secret' => $this->config->item('twitter_api_secret'),
      'callback_uri' => $this->config->item('twitter_callback_uri'),
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

    $this->authenticate->login($user);
    $this->authenticate->remember($user);

    redirect('welcome/index');
  }

  public function logout()
  {
    if ($this->authenticate->is_logged_in())
    {
      $this->authenticate->logout();
      redirect('welcome/index');
    }
  }

  public function test()
  {
    echo '<pre>';
    var_dump($this->authenticate->current_user());
    // $c = $this->current_user;
    // var_dump($c);
    // $cc = get_coded_cookie('user_id');
    // var_dump($cc);
    var_dump($this->server);
  }
}
