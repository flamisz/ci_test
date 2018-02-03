<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use League\OAuth1\Client\Server\Twitter as TwitterOauth;

class Twitter extends CI_Controller {

  private $server;

  public function __construct()
  {
    parent::__construct();

    $this->server = new TwitterOauth([
      'identifier' => 'Qfflyt0FL025wLNKbf2o1AeSd',
      'secret' => 'LLvbZM6AKXsGvEohHoL37X54sdWnfb11sU8n0vhcSUgZ9i5A8E',
      'callback_uri' => "http://ci.test/twitter/callback",
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
    if (isset($_GET['denied'])) {
      echo 'Hey! You denied the client access to your Twitter account!';
    }

    $temporaryCredentials = unserialize($this->session->temporary_credentials);
    $tokenCredentials = $this->server->getTokenCredentials($temporaryCredentials, $_GET['oauth_token'], $_GET['oauth_verifier']);
    unset($_SESSION['temporary_credentials']);
    $this->session->token_credentials = serialize($tokenCredentials);

    $user = $this->server->getUserDetails($tokenCredentials);
    echo '<pre>';
    var_dump($user);
    // redirect
  }
}
