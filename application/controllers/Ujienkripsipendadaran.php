<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ujienkripsipendadaran extends CI_Controller {

	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('PakdeEncryption_v2', NULL, 'pakde');
		
	}
	
	public function index()
	{
		$text = "Opick Tamvan Sekali";
		$alice1 = $this->pakde->diffieHellman(true);
		$alice2 = $this->pakde->diffieHellman(true);
		$bob1 = $this->pakde->diffieHellman(FALSE, $alice1['n'], $alice1['g'], $alice1['bob'], 0);
		$bob2 = $this->pakde->diffieHellman(FALSE, $alice2['n'], $alice2['g'], $alice2['bob'], 0);
		$keybob1 = $bob1['the_key'];
		$keybob2 = $bob2['the_key'];
		$keyalice1 = $this->pakde->diffieHellman(false, $alice1['n'], $alice1['g'], $bob1['bob'], $bob1['y'])['the_key'];
		$keyalice2 = $this->pakde->diffieHellman(false, $alice2['n'], $alice2['g'], $bob2['bob'], $bob2['y'])['the_key'];
		echo 'Alice1: <br>';
		print_r($alice1);
		echo '<br><br>Alice2: <br>';
		print_r($alice2);
		echo '<br><br>bob1:<br>';
		print_r($bob1);
		echo '<br><br>bob2: <br>';
		print_r($bob2);
		echo '<br><br>keyalice1: <br>';
		print_r($keyalice1);
		echo '<br><br>keyalice2: <br>';
		print_r($keyalice2);
		echo '<br><br>keybob1: <br>';
		print_r($keybob1);
		echo '<br><br>keybob2: <br>';
		print_r($keybob2);
		echo '<br>';
	}

}

/* End of file Ujienkripsipendadaran.php */



?>
