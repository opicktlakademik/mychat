<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	function __construct()
	{
		$this->load->model('Login');
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

			$user = $this->Login->check_login($username, $password);
			if ($user !== FALSE) {
				$this->session->set_userdata($user);
				rediret('Chat');
			}else {
				$this->session->set_flashdata('message', "Username or Password is incorrect");
				rediret('Welcome');
			}
		}else {
			rediret('Welcome');
		}
	}
}
