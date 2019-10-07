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
    $response['status'] = FALSE;
    $response['message'] = "Sorry";
    if ($this->input->is_ajax_request()) {
      //get data from client
      $response['status'] = TRUE;
      $response['message'] = array();
      $id_user2 = $this->input->get('id_user');
      $id_user1 = $this->session->userdata('id_user');
      $key1 = $this->input->get('key1');
      $key2 = $this->input->get('key2');
      //prepare the key
      $k1 = $this->pakde->diffieHellman(false, $key1['n'], $key1['g'], $key1['alice']);
      $k2 = $this->pakde->diffieHellman(false, $key2['n'], $key2['g'], $key2['alice']);
      //get the key to encrypt
      $enc1key = $k1['the_key'];
      $enc2key = $k2['the_key'];
      //get data message
      $q = "SELECT * FROM chat WHERE (user_1 = '$id_user1' AND user_2 = '$id_user2') OR (user_1 = '$id_user2' AND user_2 = '$id_user1') ORDER BY waktu ASC";
      $data = $this->db->query($q)->result();
      $receiver = $this->db->get_where('user', ['id_user' => $id_user2])->result();
      foreach ($data as $key) {
        $pakde2 = new PakdeEncryption_v2;
        $response['message'][] = array(
          'id_user1' => $key->user_1,
          'id_user2' => $key->user_2,
          'waktu' =>   $key->waktu,
          'pesan' => $pakde2->encrypt($key->pesan, $enc1key, $enc2key));
      }
      $response['sender_id'] = $id_user1;
      $response['sender'] = $this->session->userdata('nama');
      $response['recipient'] = $receiver[0]->nama;
      $response['key1'] = array('bob' => $k1['bob']);
      $response['key2'] = array('bob' => $k2['bob']);
      $response['page'] = $this->load->view('chat_box', null, TRUE);

      echo json_encode($response);

    }else {
      echo FALSE;
    }
  }

  function encrypt()
  {
    $c = $this->load->library('PakdeEncryption_v2');
    print_r(new PakdeEncryption_v2);
  }

  function acknowledge()
  {
    if ($this->input->is_ajax_request()) {
      // code...
      $q = "SELECT * FROM chat WHERE (user_1 = '$id_user1' AND user_2 = '$id_user2') OR (user_1 = '$id_user2' AND user_2 = '$id_user1') ORDER BY waktu ASC";
      $num = $this->db->query($q)->num_rows();
      if ($num > 0) {
        // code...
        echo $num;
      }else {
        echo 0;
      }
    }
    //echo fmod(1.40164005717698e+76, 31);
  }

  function ackReq()
  {
    if ($this->input->is_ajax_request()) {
      // code...
      $key1 = $this->input->get('key1');
      $key2 = $this->input->get('key2');
      $id_sender = $this->session->userdata('id_user');

      $k1 = $this->pakde->diffieHellman(false, $key1['n'], $key1['g'], $key1['alice']);
      $k2 = $this->pakde->diffieHellman(false, $key2['n'], $key2['g'], $key2['alice']);

      $_PAKDE_SESSION = array(
        'ID_SENDER' => $id_sender,
        'ENC1KEY' => $k1['the_key'],
        'ENC2KEY' => $k2['the_key'],
        'KEY1' => $k1,
        'KEY2' => $k1,
      );
      $_SESSION['_PAKDE_SESSION'] = $_PAKDE_SESSION;
      $_SESSION['ENC1KEY'] = $k1['the_key'];
      $_SESSION['ENC2KEY'] = $k2['the_key'];

      $response['key1'] = array('bob' => $k1['bob']);
      $response['key2'] = array('bob' => $k2['bob']);
      $response['session'] = $_PAKDE_SESSION;
      echo json_encode($response);
    }else {
      redirect('Chat', 'refresh');
    }
  }

  function inputMessage()
  {
    if ($this->input->is_ajax_request()) {
      // code...
      if ($_POST['msg']) {
				$response['status'] = TRUE;
        $user_1 = $this->session->userdata('id_user');
        $prop = $_SESSION['_PAKDE_SESSION'];
        $data = array(
          'user_1' => $user_1,
          'user_2' => $id_recipient = $this->input->post('user'),
          'waktu'  => $this->input->post('date'),
          'pesan'  => $this->pakde->decrypt($this->input->post('msg'), $_SESSION['ENC1KEY'], $_SESSION['ENC2KEY']),
        );
				$input = $this->ChatModel->insertMessage($data);
				if (!$input) {
					$response['status'] = FALSE;
					$response['message'] = "Input can't be done";
				}
        $response['data'] = $data;
        $response['session'] = array($_SESSION['ENC1KEY'], $_SESSION['ENC2KEY']);
        $response['msg'] = $this->input->post('msg');
        echo json_encode($response);
      }
    }
  }

  function test28()
  {
    $word = "4769055355570933273159035715652301236111014147492567134973190965591977673763073133221052556640401517449416339764830071661191575095322711323666852126502373669425051567401212505640449074863397630614116191322235366750915716842655269125036023716852750763512541614515552953397735037169112921174317357545430539277337335171213113";
    $key1 = 43;
    $key2 = 106;
    $dec = $this->pakde->decrypt($word, $key1, $key2);
    echo $dec[0];

    echo json_encode($dec[1]);
  }

}
