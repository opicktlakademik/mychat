<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChatModel extends CI_Model{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }

  function getAllExcept($table, $field, $id = array())
  {
    $this->db->select('*');
    $this->db->from($table);
    $this->db->where_not_in($field, $id);
    $data = $this->db->get();
    return $data;
  }

}
