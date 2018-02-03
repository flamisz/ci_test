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
}
