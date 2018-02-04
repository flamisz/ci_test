<?php

class User_model extends CI_Model {

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
    $remember_token = bin2hex(random_bytes(32));

    $update_data = [
      'remember_digest' => password_hash($remember_token, PASSWORD_DEFAULT)
    ];

    $this->db->update('users', $update_data, ['id' => $user->id]);

    return $remember_token;
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
