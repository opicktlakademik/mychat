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
    return $this->public;
  }


  function encrypt($value = NULL, $key_chiper_1 = 2, $key_chiper_2 = 2)
  {
    if ($value !== NULL) {
      $encrypt1 = $this->caesar_chiper($value, $this->dict1);
      $encrypt2 = $this->matrix_encryption($encrypt1, $this->dict2);
      $packaged = $this->packing($encrypt2, $key_chiper_1, $key_chiper_2);
      $unpacking = $this->unpacking($packaged, $key_chiper_1, $key_chiper_2);
      return array('caesar' => $encrypt1, 'matrix' => $encrypt2, 'package' => $packaged, 'unpack' => $unpacking, 'reportpack' => $this->reportpack, 'reportunpack' => $this->reportunpack);
    }else {
      return NULL;
    }
  }
  private function fisher_yates_shuffle()
  {
      //inisialisasi dict1
      $this->dict1 = str_split($this->public);
      $n = count($this->dict1);
      //fisher_yates_shuffle algorithm implementation
      for ($i = $n - 1; $i >= 0 ; $i--) {
        $j = rand(0, $i);
        $temp = $this->dict1[$i];
        $this->dict1[$i] = $this->dict1[$j];
        $this->dict1[$j] = $temp;
      }
      //inisialisasi dict2
      $num = 0;
      for ($n=0; $n < 10; $n++) {
        for ($m=0; $m < 10; $m++) {
          if (isset($this->dict1[$num])) {
            // code...
            $this->dict2[$n][$m] = $this->dict1[$num];
          }
          $num++;
        }
      }
    /*  $sizeDict2Row = sizeof($this->dict2) - 1;
      $sizeDict2Field = sizeof($this->dict2[$sizeDict2Row]) - 1 ;
      $this->dict2[$sizeDict2Row][$sizeDict2Field] = "~";*/
      //fisher_yates_shuffle untuk dict2
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
    //cipertext dict1 caesar_chiper -> cipertext dict1 matrix_encryption
    $cd1 = $this->caesar_chiper($plaintext_d1, $this->dict_public_1, $key1);
    $cd1_m = $this->matrix_encryption($cd1, $this->dict_public_2);

    //cipertext dict2 caesar_chiper -> cipertext dict2 matrix_encryption
    $plaintext_d2 = "";
    for ($i=0; $i < sizeof($this->dict2); $i++) {
      $plaintext_d2 .= implode("", $this->dict2[$i]);
    }
    $cd2 = $this->caesar_chiper($plaintext_d2, $this->dict1, $key2);
    $cd2_m = $this->matrix_encryption($cd2, $this->dict_public_2);

    //Packaging
    $len_dict1 = strlen($cd1_m);
    $len_cipher = strlen($value);

    $piece_dict1_1 = substr($cd1_m, 0, $len_dict1/2);
    $piece_dict1_2 = substr($cd1_m, $len_dict1/2 , $len_dict1);

    $piece_cipher_1 = substr($value, 0, $len_cipher/2);
    $piece_cipher_2 = substr($value, $len_cipher/2, $len_cipher);

    $full_cipher = $piece_dict1_1.$piece_cipher_1.$cd2_m.$piece_cipher_2.$piece_dict1_2;
    //return $full_cipher;
    $report = array('dict1' => $plaintext_d1,
                    'dict1_caesar' => $cd1,
                    'dict1_matrix' => $cd1_m,
                    'dict2'        => $plaintext_d2,
                    'dict2_casar'  => $cd2,
                    'dict2_matrix' => $cd2_m,
                    'ciphertext'   => $value,
                    'full_ciphertext' => $full_cipher,
                    'dict1_1'      => $piece_dict1_1,
                    'dict1_2'      => $piece_dict1_2,
                    'cipher1'      => $piece_cipher_1,
                    'cipher2'      => $piece_cipher_2,
                    'public'       => $this->public
                  );
    $this->reportpack = $report;
    return $full_cipher;
  }

   function unpacking($ciphertext_complete, $key1, $key2)
  {
    $len_ciphertext_complete = strlen($ciphertext_complete);
    $len_public = strlen($this->public);
    $len_ciphertext = $len_ciphertext_complete - ($len_public * 4);
    //unpacking dict1 from encrypted
    $dict1_1 = substr($ciphertext_complete, 0, $len_public);
    $dict1_2 = substr($ciphertext_complete, $len_ciphertext_complete - $len_public, $len_ciphertext_complete);
    $dict1 = $dict1_1.$dict1_2;
    //unpacking ciphertext from encrypted
    $ciphertext_1 = substr($ciphertext_complete, $len_public, $len_ciphertext / 2);
    $ciphertext_2 = substr($ciphertext_complete, $len_ciphertext_complete - ($len_public + $len_ciphertext / 2 ), $len_ciphertext / 2);
    $ciphertext = $ciphertext_1.$ciphertext_2;
    //unpacking dict2 from encrypted
    $del1_dict2 = $len_public + $len_ciphertext / 2;
    $dict2 = substr($ciphertext_complete, $del1_dict2, $len_public*2);
    //decrypting dict1 taken from $ciphertext_complete / string encrypted and packed for decrypting ciphertext
    $dict1_4_caesar_step_1 = $this->matrix_decryption($dict1, $this->dict_public_2);
    $dict1_4_caesar = $this->caesar_cipher_decryption($dict1_4_caesar_step_1, $this->dict_public_1, $key1);
    //decrypting dict2 taken from $ciphertext_complete / string encrypted and packed for decrypting ciphertext
    $dict2_4_matrix_step_1 = $this->matrix_decryption($dict2, $this->dict_public_2);
    $dict2_4_matrix = $this->caesar_cipher_decryption($dict2_4_matrix_step_1, str_split($dict1_4_caesar), $key2);
    //decrypting ciphertext using $dict1_4_caesar and $dict2_4_matrix
    $decrypted_by_matrix = $this->matrix_decryption($ciphertext, $dict2_4_matrix);
    $decrypted_plaintext = $this->caesar_cipher_decryption($decrypted_by_matrix, str_split($dict1_4_caesar), 0, FALSE);

    $report = array('dict1_encrypted' => $dict1,
                    'dict1_encrypted_1' => $dict1_1,
                    'dict1_encrypted_2' => $dict1_2,
                    'dict1_decrypted_matrix' => $dict1_4_caesar_step_1,
                    'dict1_decrypted_caesar' => $dict1_4_caesar,
                    'dict2_encrypted'  => $dict2,
                    'dict2_decrypted_matrix' => $dict2_4_matrix_step_1,
                    'dict2_decrypted_caesar' => $dict2_4_matrix,
                    'cipher' => $ciphertext,
                    'cipher1' => $ciphertext_1,
                    'cipher2' => $ciphertext_2,
                    'plaintext_matrix' => $decrypted_by_matrix,
                    'plaintext' => $decrypted_plaintext,
                    'public2'       => $this->dict_public_2,
                  );
    $this->reportunpack = $report;
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

  function diffieHellman($a = NULL, $n = NULL, $g = NULL)
  {
    $bob_a = $a;
    $bob_n = $n;
    $bob_g = $g;
    $bob_x = rand(20, 100);
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
    $bob_a = pow($bob_g, $bob_x) % $bob_n;

    return array($bob_a, $bob_n, $bob_g);
  }
}
 ?>
