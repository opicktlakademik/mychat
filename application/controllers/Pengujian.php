<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengujian extends CI_Controller
{
	public $text = [
		"Opick Tamvan Sekali, ya...!",
	];
	

	public function __construct()
	{
		parent::__construct();
		$this->load->library('PakdeEncryption_v2', NULL, 'pakde');
	}

	function index()
	{
		$this->load->view('pengujian_kode');
	}

	function index2()
	{
		$this->load->view('pengujian_kode2');
	}

	function testphp()
	{
		ob_start();
		$text = $_POST['text'];
		$text_lengh = strlen($text);
		var_dump($this->pakde->decrypt($text, (int)$_SESSION['key1'], (int)$_SESSION['key2']));
		$data['data'] = $_SESSION;
		$data['upkg'] = ob_get_clean();
		//var_dump($data);
		//echo json_encode(['key1' => $_SESSION['key1'],'key2' => $_SESSION['key2'], 'text' => $text]);
		// var_dump((int)$_SESSION['key1']);
		//echo $this->pakde->getDict();
		echo json_encode($data);
	}

	public function akg()
	{
		$key1 = $_GET['key1'];
		$key2 = $_GET['key2'];
		$key1 = $this->pakde->diffieHellman(false, $key1['n'], $key1['g'], $key1['alice']);
		$key2 = $this->pakde->diffieHellman(false, $key2['n'], $key2['g'], $key2['alice']);
		$_SESSION['key1'] = $key1['the_key'];
		$_SESSION['key2'] = $key2['the_key'];
		$_SESSION['x1'] = $key1['y'];
		$_SESSION['X1']	= $key1['bob'];
		$_SESSION['X2']	= $key2['bob'];
		$_SESSION['x2']	= $key2['y'];
		$data = ['bob1' => $key1['bob'], 'bob2' => $key2['bob'], 'key1' => $key1['the_key'], 'key2' => $key2['the_key']];
		echo json_encode($data);
	}

	function directTest()
	{
		$word = "0646715732124750614828730319453551684266640924215643673117403801265537750563770933405524BO63328393066502475115177137147014432695617700819155846276125094042043523537637631049677429456564001220683662590503520726417355064838576034184331225472142116028355821575868140413495811221633077234542730741552606239007644536929024110087065592523361820";
		$data = var_dump($word);
		echo "s"."p";
	}

	function getText()
	{
		$response = ['messages' => [[]], 'properties'];
		$key1 = $_GET['key1'];
		$key2 = $_GET['key2'];
		$key1 = $this->pakde->diffieHellman(false, $key1['n'], $key1['g'], $key1['alice']);
		$key2 = $this->pakde->diffieHellman(false, $key2['n'], $key2['g'], $key2['alice']);
		for ($i=0; $i < sizeof($this->text) ; $i++) {
			$result = $this->pakde->encryptTest2($this->text[$i], $key1['the_key'], $key2['the_key']);
			$response['messages'][$i]['pkg'] = $result['pkg'];
			$response['messages'][$i]['mc'] = $result['mc'];
			$response['messages'][$i]['cc'] = $result['cc'];
			$response['dictpublic'] = $result['dictpublic'];
			$response['dict1public'] = $result['dict1public'];
			$response['dict2public'] = $result['dict2public'];
			$response['dict1'] = $result['dict1'];
			$response['dict2'] = $result['dict2'];
			$response['dictspace'] = $result['dictspace'];
			$response['dict1caesar'] = $result['dict1caesar'];
			$response['dict1matrix'] = $result['dict1matrix'];
			$response['dict2caesar'] = $result['dict2caesar'];
			$response['dict2matrix'] = $result['dict2matrix'];
		}
		$response['bob1'] = $key1['bob'];
		$response['bob2']= $key2['bob'];
		$response['properties']['key1'] = $key1;
		$response['properties']['key2'] = $key2;
		echo json_encode($response); 

	}
}
