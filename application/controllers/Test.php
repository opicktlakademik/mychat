<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    //Codeigniter : Write Less Do More
  }

  function index()
  {

  }

  function encrypt()
  {
    $this->load->view('encrypt');
  }

  function testMessage()
  {
    $q = "SELECT * FROM chat WHERE (user_1 = '1' AND user_2 = '4') OR (user_1 = '4' AND user_2 = '1')";
    $data = $this->db->query($q);
    print_r($data->result());
  }

}
