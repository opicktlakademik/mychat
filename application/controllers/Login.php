<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
	{
    parent::__construct();
		$this->load->model('LoginModel');
	}
	public function index()
	{
		if ($this->session->userdata('loggin') AND $this->session->userdata('loggin') === TRUE AND $this->session->userdata('id_user') !== NULL) {
			rediret('Chat');
		}else {
			$this->load->view('login_view');
		}
	}

	public function check_login()
	{
		if (isset($_POST['username']) AND isset($_POST['password'])) {
			$username = $this->input->post('username');
			$password = md5($this->input->post('password'));

			$user = $this->LoginModel->check_login($username, $password);
			if ($user !== FALSE) {
				$this->session->set_userdata($user);
				redirect('Chat');
			}else {
				$this->session->set_flashdata('message', "Username or Password is incorrect");
				redirect('Login');
			}
		}else {
			redirect('Login');
		}
	}

  public function logout()
  {
    $this->session->sess_destroy();
    $this->session->set_flashdata('message', "Thanks for coming");
    redirect('Login');
  }
}
