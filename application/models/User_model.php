<?php

class User_model extends CI_Model {

  public $remember_token;

  public function __construct()
  {
    $this->load->database();
  }

  public function from_oauth($oauth_user)
  {
    return $this->find_by('uid', $oauth_user['uid']) ?:
                          $this->create_from_oauth($oauth_user);
  }

  public function find_by($attribute, $value)
  {
    $query = $this->db->get_where('users', [$attribute => $value], 1);

    return $query->row();
  }

  public function create_from_oauth($oauth_user)
  {
    return $this->db->insert('users', $oauth_user) ?
                  $this->find_by('id', $this->db->insert_id()) : FALSE;
  }

  public function remember($user)
  {
    $this->remember_token = bin2hex(random_bytes(32));

    $update_data = [
      'remember_digest' => password_hash($this->remember_token, PASSWORD_DEFAULT)
    ];

    $this->db->update('users', $update_data, ['id' => $user->id]);
  }

  public function is_authenticated($user, $token)
  {
    if (! $user->remember_digest)
    {
      return FALSE;
    }

    return password_verify($token, $user->remember_digest);
  }
}
