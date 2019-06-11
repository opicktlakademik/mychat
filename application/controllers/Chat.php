<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    if ($this->session->userdata('loggin') !== TRUE OR $this->session->userdata('nama') === NULL) {
      $this->session->set_flashdata('message', "<b style='color:red'>Access Denied!</b>");
      redirect('Login');
    }
    $this->load->model(array('ChatModel'));
    $this->load->library('PakdeEncryption_v2' , NULL, 'pakde');
  }

  function index()
  {
    $data['user'] = $this->ChatModel->getAllExcept('user', 'id_user', array('id_user' => $this->session->userdata('id_user')))->result();
    $this->load->view('chat_view', $data);
  }

  function getMessage()
  {
    if (isset($_GET['id_user'])) {
      $id_user = $this->input->get('id_user');
    }
  }

  function getMessageBox()
  {
    // code...
    if ($_GET['getBox'] !== NULL AND isset($_GET['getBox']) AND $_GET['getBox'] = "Okk..") {
      $page = $this->load->view('chat_box.php', NULL, TRUE);
      $key1Client = $_GET['key1'];
      $key1Server = $this->pakde->diffieHellman();

    }else {
      echo FALSE;
    }
  }

  function encrypt()
  {
    $this->load->view('encrypt');
  }

}
