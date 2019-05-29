class PakdeEnrcyption {

  constructor(){
    this.public_alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890.,;:-?!()[]\"'\\/|";
    this.dict_public_1;
    this.dict_public_2 = new Array();
    this.dict1;
    this.dict2 = new Array();
    this.dict3 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    this.initializing();
  }

  encrypt(){
    var word1 = this.caesar_cipher("AB C", this.dict_public_1, 0);
    var word2 = this.matrix_encryption(word1, this.dict_public_2);
    var packed = this.packing(word2, 2, 2);
    var unpacked = this.unpacking(packed.full_ciphertext, 2, 2);
    var report = {
      packing:packed,
      //encrypt1:word1,
    //  encrypt2:word2,
      unpack:unpacked,
    };
    return report;
  }

  initializing(){
    //split the aplha
    this.dict_public_1 = this.public_alpha.split("");
    //make dict1 using fisher_yates_shuffle
    this.dict1 = this.public_alpha.split("");
    var n = this.dict1.length;
  /*  for (var i = n - 1 ; i >= 0 ; i--) {
      var j = Math.floor(Math.random() * i);
      var temp = this.dict1[i];
      this.dict1[i] = this.dict1[j];
      this.dict1[j] = temp;
    }*/
    //make 2 dimension array of dict public 2 and dict2
    var num = 0;
    var exit = false;
    for (var i = 0; i < 10; i++) {
      if (exit) {
        break;
      }else{
        this.dict_public_2[i] = new Array();
        this.dict2[i] = new Array();
      }
      for (var j = 0; j < 10; j++) {
        if (typeof this.dict_public_1[num] !== 'undefined') {
          this.dict_public_2[i][j] = this.dict_public_1[num];
          this.dict2[i][j] = this.dict_public_1[num];
          num++;
        }else {
          exit = true;
          break;
        }
      }
    }
    //shuffle dict2 using fisher_yates_shuffle
    /*for (var i = 0; i < this.dict2.length; i++) {
      n = this.dict2.length - 1;
      for (var j = n; j>= 0; j--) {
        var k = Math.floor(Math.random() * j);
        temp = this.dict2[i][j];
        this.dict2[i][j] = this.dict2[i][k];
        this.dict2[i][k] = temp;
      }
    }*/
    //make dict 3 for space in the matrix_decryption
    this.dict3 = this.dict3.split("");
  }

  diffieHellman() {
    var temp1 = 5, temp2 = 7, j = 2;
    var prime = [temp1, temp2];
    // get the prime
    for (var i = 0; i < 50; i++) {
      temp1 += 6;
      if (temp1 % 5 != 0 && temp1 % 7 != 0) {
        prime[j] = temp1;
        j++
      }
      temp2 += 6;
      if (temp2 % 7 != 0 && temp2 % 5 != 0) {
        prime[j] = temp2;
        j++
      }
    }
    //console.log(prime);
    // menentukan x, n, g dimana g < n
    var index_n = Math.floor(Math.random() * (prime.length - 8)) + 8;
    var index_g = Math.floor(Math.random() * index_n);
    var n = prime[index_n];
    var g = prime[index_g];
    var x = Math.floor(Math.random() * (100 - 20 + 1)) +20;
    //count a = g^x mod n
    var a = Math.pow(g, x) % n;
    var ret = [a, n, g];
    console.log("n = "+ n);
    console.log("g = " + g);
    console.log("x = "+ x);
    console.log("a = " + a);
    return ret;
  }

  caesar_cipher(text, alphabet = 0, key = 0){

    var dict = alphabet;
    var enc = "";
    var len_dict = parseInt(dict.length);
    text = text.replace("\n", "|");
    var plaintext = text.split("");

    if (key === 0) {
      key = parseInt(plaintext.length);
    }

    for (var i = 0; i < plaintext.length; i++) {
      var index = dict.indexOf(plaintext[i]);
      if (index !== false && plaintext[i] !== " ") {
        var replacer = (parseInt(index) + key) % len_dict;
        enc += dict[replacer];
      }else if (plaintext[i] === " ") {
        enc += " ";
      }else {
        enc += "#";
      }
    }
    return enc;
  }

  matrix_encryption(text, alphabet){

    var dict = alphabet;
    var len_dict = alphabet.length;
    var plaintext = text.split("");
    var len_text  = plaintext.length;
    var enc = "";

    for (var i = 0; i < len_text; i++) {
      if (plaintext[i] !== " ") {
        for (var j = 0; j < len_dict; j++) {
          for (var k = 0; k < dict[j].length; k++) {
            var temp = dict[j][k];
            if (temp === plaintext[i]) {
              var w = j.toString() + k.toString();
              enc += w;
              break;
            }
          }
        }
      }else if (plaintext[i] === " ") {
        var rand1 = Math.floor(Math.random() * 25);
        var rand2 = Math.floor(Math.random() * 25);
        enc += this.dict3[rand1]+this.dict3[rand2];
      }else {
        enc += "#";
      }
    }
    return enc;
  }

  packing(ciphertext, key1, key2){
    var plaintext_d1 = this.dict1.join("");
    //ciphertext of dict1 (caesar_cipher->matrix_encryption)
    var cd1 = this.caesar_cipher(plaintext_d1, this.dict_public_1, key1);
    var cd1_m = this.matrix_encryption(cd1, this.dict_public_2);

    //ciphertext of dict2
    var plaintext_d2 = "";
    for (var i = 0; i < this.dict2.length; i++) {
      plaintext_d2 += this.dict2[i].join("");
    }
    var cd2 = this.caesar_cipher(plaintext_d2, this.dict1, key2);
    var cd2_m = this.matrix_encryption(cd2, this.dict_public_2);

    //packing
    var len_dict1 = cd1_m.length;
    var len_dict2 = cd2_m.length;
    var len_ciphertext = ciphertext.length;

    var piece_dict1_1 = cd1_m.substring(0, len_dict1/2);
    var piece_dict1_2 = cd1_m.substring(len_dict1/2, len_dict1);

    var piece_cipher1 = ciphertext.substring(0, len_ciphertext/2);
    var piece_cipher2 = ciphertext.substring(len_ciphertext/2, len_ciphertext);

    var full_ciphertext = piece_dict1_1+piece_cipher1+cd2_m+piece_cipher2+piece_dict1_2;
    //return full_ciphertext;
    var object = {
      plaintext_d1:plaintext_d1,
      len_plaintext_d1: plaintext_d1.length,
      len_dict1:len_dict1,
      dict1_caesar:cd1,
      dict1_matrix:cd1_m,
      piece_dict1_1:piece_dict1_1,
      piece_dict1_2:piece_dict1_2,
      plaintext_d2:plaintext_d2,
      len_dict2:len_dict2,
      dict2_casar:cd2,
      dict2_matrix:cd2_m,
      ciphertext:ciphertext,
      len_ciphertext:len_ciphertext,
      len_ciphertext_complete:full_ciphertext.length,
      full_ciphertext:full_ciphertext,
      dict_public_2:this.dict_public_2,
      len_dict1_caesar:cd1.length,
      len_dict2_caesar:cd2.length,
    };
    console.log("Packing");
    console.log(object);
    return object;
  }

  unpacking(ciphertext_complete, key1, key2){
    var len_ciphertext_complete = parseInt(ciphertext_complete.length);
    var len_public = parseInt(this.public_alpha.length);
    var len_ciphertext = len_ciphertext_complete - (len_public * 4);
    //unpaacking dict1 from ciphertext_complete
    var dict1_1 = ciphertext_complete.substring(0, len_public);
    var dict1_2 = ciphertext_complete.substring(len_ciphertext_complete-len_public, len_ciphertext_complete);
    var dict1_cipher = dict1_1+dict1_2;
    //unpacking ciphertext from ciphertext_complete
    var ciphertext_1 = ciphertext_complete.substring(len_public, len_public + len_ciphertext / 2);
    var ciphertext_2 = ciphertext_complete.substring(len_ciphertext_complete - (len_public + len_ciphertext / 2), len_ciphertext_complete - len_public);
    var ciphertext = ciphertext_1+ciphertext_2;
    //unpacking dict2 from ciphertext_complete
    var dict2_cipher = ciphertext_complete.substring(len_public + len_ciphertext / 2, len_public + (len_ciphertext / 2) + (len_public * 2));
    //decrypting dict1
    var dict1_dec_m = this.matrix_decryption(dict1_cipher, this.dict_public_2);
    var dict1 = this.caesar_cipher_decryption(dict1_dec_m, this.dict_public_1, key1);
    //decrypting dict2
    var dict2_dec_m = this.matrix_decryption(dict2_cipher, this.dict_public_2);
    var dict2 = this.caesar_cipher_decryption(dict2_dec_m, dict1.split(""), key2);
    //decrypting ciphertext
    var plaintext_dec_m = this.matrix_decryption(ciphertext, dict2.split(""));
    var plaintext = 'DISABLED';//this.caesar_cipher_decryption(plaintext_dec_m, dict1.split(""), 0, false);
    //returning
    //return plaintext;
    var object = {
      len_ciphertext_complete:len_ciphertext_complete,
      len_ciphertext:len_ciphertext,
      len_public:len_public,
      plaintext:plaintext,
      plaintext_dec_m: plaintext_dec_m,
      dict1_cipher:dict1_cipher,
      len_dict1_cipher:dict1_cipher.length,
      dict1_1:dict1_1,
      dict1_2:dict1_2,
      dict1_dec_m:dict1_dec_m,
      dict1_dec_c:dict1,
      dict2_cipher:dict2_cipher,
      dict2_dec_m: dict2_dec_m,
      dict2_dec_c: dict2,
      len_dict2_cipher:dict2_cipher.length,
      final_ciphertext:ciphertext_complete,
      len_1 :len_public + (len_ciphertext/2),
      len_2 : len_public * 2,
      ciphertext:ciphertext,
      ciphertext_1:ciphertext_1,
      ciphertext_2:ciphertext_2
    }
    console.log("unpack");
    console.log(object);
    return object;
  }

  matrix_decryption(ciphertext, alphabet){

    if (Array.isArray(alphabet)) {
      var dict2 = alphabet;
    }else {
      var num = 0;
      var dict = alphabet.split("");
      var exit = false;
      var dict2 = new Array();
      for (var i = 0; i < 10; i++) {
        if (!exit) {
          dict2[i] = new Array();
        }else{
          break;
        }
        for (var j = 0; j < 10; j++) {
          if (typeof dict[num] !== 'undefined') {
            dict2[i][j] = dict[num];
            num++;
          }else {
            exit = true;
            break;
          }
        }
      }

    }

    var word = ciphertext.split("");
    var result = "";
    var j = 1;

    for (var i = 0; i < word.length; i++) {
      var check = this.dict3.indexOf(word[i]);
      if (check !== false) {
        result += dict2[word[i]][word[j]];
      }else {
        result += " ";
      }
      j += 2;
      i++;
    }
    return result;
  }

  caesar_cipher_decryption(ciphertext, alphabet, key = 0, is_dict = true){

      var result = "";
      var word = ciphertext.split("");
      var dict1 = alphabet;
      var len_dict1 = parseInt(dict1.length);
      if (key === 0) {
        key = parseInt(word.length);
      }
      for (var i = 0; i < word.length; i++) {
        var index = dict1.indexOf(word[i]);
        if (index !== false) {
          var replacer = (parseInt(index) - key) % len_dict1;
          if (replacer < 0) {
            var y = Math.abs(replacer);
            var z = len_dict1 - y;
            result += dict1[z];
          }else {
            result += dict1[replacer];
          }
        }else if (word[i] == " ") {
          result += " ";
        }else {
          result += "#";
        }
      }
      if (!is_dict) {
        result = result.replace("|", "\n");
      }
      return result;
  }
}
