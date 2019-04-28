<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller{

  public function __construct()
  {
    parent::__construct();
    if ($this->session->userdata('loggin') !== TRUE OR $this->session->userdata('nama') === NULL) {
      redirect('Login');
    }
    $this->load->model(array('ChatModel'));
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
      echo json_encode($page);
    }else {
      echo FALSE;
    }
  }

}