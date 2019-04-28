<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LoginModel extends CI_Model{

  function check_login($username, $password)
  {
    $data;
    $check = $this->db->get_where('user', array('username' => $username, 'password' => $password));
    if ($check->num_rows() > 0) {
      foreach ($check->result() as $key) {
          $data = array(
            'nama' => $key->nama,
            'loggin' => TRUE,
            'id_user' => $key->id_user,
          );
      }
      return $data;
    }else {
      return FALSE;
    }
  }

}
