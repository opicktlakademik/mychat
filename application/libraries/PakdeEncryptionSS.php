<?php
/**
 * TLS Encryption
 */
class PakdeEncryption_v2
{
  public  $public = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.,;:-?!()[]\"'\/|";
  public  $dict_public_1;
  public  $dict_public_2;
  private $dict1;
  private $dict2;
  private $dict3 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  public  $reportpack, $reportunpack, $reportencrypt, $reportdecrypt;

  function __construct()
  {

    $this->public_initializing();
    $this->fisher_yates_shuffle();
  }


  public function encrypt($value = NULL, $key_chiper_1, $key_chiper_2)
  {
    if ($value !== NULL) {

      $encrypt1 = $this->caesar_chiper($value, $this->dict1);
      $encrypt2 = $this->matrix_encryption($encrypt1, $this->dict2);
      $packaged = $this->packing($encrypt2, $key_chiper_1, $key_chiper_2);
      return $packaged;

    }else {
      return NULL;
    }
  }

  public function decrypt($ciphertext, $key1, $key2)
  {
    if ($ciphertext !== NULL) {
      $plaintext = $this->unpacking($ciphertext, $key1, $key2);
      return $plaintext;
    }else {
      return NULL;
    }
    

    
  }

  private function fisher_yates_shuffle()
  {
      $this->dict1 = str_split($this->public);
      $n = count($this->dict1);
      for ($i = $n - 1; $i >= 0 ; $i--) {
        $j = rand(0, $i);
        $temp = $this->dict1[$i];
        $this->dict1[$i] = $this->dict1[$j];
        $this->dict1[$j] = $temp;
      }
      $num = 0;
      for ($n=0; $n < 10; $n++) {
        for ($m=0; $m < 10; $m++) {
          if (isset($this->dict1[$num])) {
            $this->dict2[$n][$m] = $this->dict1[$num];
          }
          $num++;
        }
      }
      for ($i = 0 ; $i < count($this->dict2) ; $i++) {
        $n = count($this->dict2[$i]);
        for ($j = $n - 1; $j >= 0 ; $j--) {
          $k = rand(0, $j);
          $temp = $this->dict2[$i][$j];
          $this->dict2[$i][$j] = $this->dict2[$i][$k];
          $this->dict2[$i][$k] = $temp;
        }
      }
  }

  function get_dict()
  {
    return count($this->dict2[1]);
	}
	
  private function public_initializing()
  {
    $this->dict_public_1 = str_split($this->public);
    $num = 0;
    for ($i=0; $i < 10; $i++) {
      for ($j=0; $j < 10; $j++) {
        if (isset($this->dict_public_1[$num])) {
          $this->dict_public_2[$i][$j] = $this->dict_public_1[$num];
          $num++;
        }
      }
    }
  }

  private function caesar_chiper($value, $alphabet, $key_chiper = 0)
  {

    $dict = $alphabet;
    $enc = "";
    $plaintext = str_replace(PHP_EOL, "|", $value);
    $word = str_split($plaintext);
    if ($key_chiper !== 0) {
      $char_sum = $key_chiper;
    }else {
      $char_sum = sizeof($word);
    }

    for ($i=0; $i < sizeof($word) ; $i++) {
      $index = array_search($word[$i], $dict);
      if ($index !== FALSE && $word[$i] != ' ') {
        $replacer = ($index + $char_sum) % sizeof($dict);
        $enc .= $dict[$replacer];
      }elseif($word[$i] === ' '){
        $replacer = ' ';
        $enc .= ' ';
      }else {
        $enc .= "#";
      }
    }
    return $enc;
  }

  private function matrix_encryption($value, $alphabet)
  {
    $dict = $alphabet;
    $word = str_split($value);
    $char_sum = sizeof($word);
    $enc = "";

    for ($i=0; $i < $char_sum ; $i++) {
      if ($word[$i] !== " ") {
        for ($j=0; $j < sizeof($dict) ; $j++) {
          for ($k=0; $k < sizeof($dict[$j]) ; $k++) {
            $temp = $dict[$j][$k];
            if ($temp === $word[$i]) {
              $enc .= $j.$k;
              break;
            }
          }
        }
      }elseif($word[$i] === " "){
        $space = str_split($this->dict3);
        $enc .= $space[rand(0, strlen($this->dict3)-1)];
        $enc .= $space[rand(0, strlen($this->dict3)-1)];
      }else {
        $enc .= "#";
      }
    }
    return $enc;
  }

  private function packing($value, $key1, $key2)
  {
    $plaintext_d1 = implode("", $this->dict1);
    $cd1 = $this->caesar_chiper($plaintext_d1, $this->dict_public_1, $key1);
    $cd1_m = $this->matrix_encryption($cd1, $this->dict_public_2);
    $plaintext_d2 = "";
    for ($i=0; $i < sizeof($this->dict2); $i++) {
      $plaintext_d2 .= implode("", $this->dict2[$i]);
    }
    $cd2 = $this->caesar_chiper($plaintext_d2, $this->dict1, $key2);
    $cd2_m = $this->matrix_encryption($cd2, $this->dict_public_2);
    $len_dict1 = strlen($cd1_m);
    $len_cipher = strlen($value);
    $piece_dict1_1 = substr($cd1_m, 0, $len_dict1/2);
    $piece_dict1_2 = substr($cd1_m, $len_dict1/2 , $len_dict1);
    $piece_cipher_1 = substr($value, 0, $len_cipher/2);
    $piece_cipher_2 = substr($value, $len_cipher/2, $len_cipher);
    $full_cipher = $piece_dict1_1.$piece_cipher_1.$cd2_m.$piece_cipher_2.$piece_dict1_2;
    return $full_cipher;
  }

   function unpacking($ciphertext_complete, $key1, $key2)
  {
    $len_ciphertext_complete = strlen($ciphertext_complete);
    $len_public = strlen($this->public);
    $len_ciphertext = $len_ciphertext_complete - ($len_public * 4);
    $dict1_1 = substr($ciphertext_complete, 0, $len_public);
    $dict1_2 = substr($ciphertext_complete, $len_ciphertext_complete - $len_public, $len_ciphertext_complete);
    $dict1 = $dict1_1.$dict1_2;
    $ciphertext_1 = substr($ciphertext_complete, $len_public, $len_ciphertext / 2);
    $ciphertext_2 = substr($ciphertext_complete, $len_ciphertext_complete - ($len_public + $len_ciphertext / 2 ), $len_ciphertext / 2);
    $ciphertext = $ciphertext_1.$ciphertext_2;
    $del1_dict2 = $len_public + $len_ciphertext / 2;
    $dict2 = substr($ciphertext_complete, $del1_dict2, $len_public*2);
    $dict1_4_caesar_step_1 = $this->matrix_decryption($dict1, $this->dict_public_2);
    $dict1_4_caesar = $this->caesar_cipher_decryption($dict1_4_caesar_step_1, $this->dict_public_1, $key1);
    $dict2_4_matrix_step_1 = $this->matrix_decryption($dict2, $this->dict_public_2);
    $dict2_4_matrix = $this->caesar_cipher_decryption($dict2_4_matrix_step_1, str_split($dict1_4_caesar), $key2);
    $decrypted_by_matrix = $this->matrix_decryption($ciphertext, $dict2_4_matrix);
    $decrypted_plaintext = $this->caesar_cipher_decryption($decrypted_by_matrix, str_split($dict1_4_caesar), 0, FALSE);
    return $decrypted_plaintext;
  }

  private function matrix_decryption($ciphertext, $alphabet)
  {
    $dict2 = array();
    $num = 0;
    if (is_array($alphabet)) {
      $dict2 = $alphabet;
    }else {
      $dict = str_split($alphabet);
      for ($c=0; $c < 10; $c++) {
        for ($d=0; $d < 10; $d++) {
          if (isset($dict[$num])) {
            $dict2[$c][$d] = $dict[$num];
          }
          $num++;
        }
      }
    }
    $word = str_split($ciphertext);
    $space = str_split($this->dict3);
    $result = "";
    $j = 1;
    for ($i=0; $i < sizeof($word); $i++) {
      if ($a = array_search($word[$i], $space) === false) {
        $result .= $dict2[$word[$i]][$word[$j]];
      }else{
        $result .= " ";
      }
      $j+=2;
      $i++;
    }
    return $result;
  }

  private function caesar_cipher_decryption($value, $alphabet, $key = 0, $is_dict = TRUE)
  {
    $result = "";
    $word = str_split($value);
    $dict1 = $alphabet;
    if ($key === 0) {
      $key = sizeof($word);
    }

    for ($i=0; $i < sizeof($word); $i++) {
      $index = array_search($word[$i], $dict1);
      if ($index !== FALSE) {
        $replacer = ($index - $key) % sizeof($dict1);
        if ($replacer < 0) {
          $x = sizeof($dict1);
          $y = abs($replacer);
          $z = $x - $y;
          $replacer2 = $dict1[$z];
          $result .= $replacer2;
        }else {
          $result .= $dict1[$replacer];
        }
      }elseif($word[$i] == ' '){
        $replacer = ' ';
        $result .= ' ';
      }else{
        $result .= "#";
      }
    }
    if (!$is_dict) {
      $result = str_replace("|", '<br>', $result);
    }
    return $result;
  }

  function diffieHellman($generate_key = true, $n = 0, $g = 0, $alice = 0)
  {
    $y = rand(20, 100);
    if ($generate_key) {
      $temp1 = 5;
      $temp2 = 7;
      $j = 2;
      $prime = array($temp1, $temp2);
      for ($i=0; $i < 50; $i++) {
        $temp1 += 6;
        if ($temp1 % 5 != 0 AND $temp1 % 7 != 0) {
          $prime[$j] = $temp1;
          $j++;
        }
        $temp2 += 6;
        if ($temp2 % 5 != 0 AND $temp2 % 7 != 0) {
          $prime[$j] = $temp2;
          $j++;
        }
       }
      $index_n = rand(8, sizeof($prime) - 1);
      $index_g = rand(0, $index_n);
      $bob_n = $prime[$index_n];
      $bob_g = $prime[$index_g];
      $bob_a = pow($bob_g, $y) % $bob_n;
      return array($bob_a, $bob_n, $bob_g);
    }else {
      $bob = bcmod(bcpow($g, $y), $n);
      $the_key = bcmod(bcpow($alice, $y), $n);
      return array('y' => $y, 'bob' => $bob, 'the_key' => $the_key);
    }
  }
}
 ?>
