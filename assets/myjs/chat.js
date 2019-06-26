$(window).ready(function(){
  /* var bob = new Big(144);
  var alice = 153;
  var count = bob.pow(alice).mod(90);
  console.log(count.toString()); */
})
$('.list-click').click(function(){
  var id_user = $(this).attr('data-id');
  var pakde = new PakdeEnrcyption;
  var key1 = pakde.diffieHellman(true);
  var key2 = pakde.diffieHellman(true);
  var key1_to_send = {'n':key1.n, 'g':key1.g, 'alice':key1.alice};
  var key2_to_send = {'n':key2.n, 'g':key2.g, 'alice':key2.alice};
  console.log(key1);
  console.log(key2);

  //request to server
  $.get('Chat/getMessageBox', {id_user:id_user, key1:key1_to_send, key2:key2_to_send}, function(e, success){
    if (success) {

      var data = JSON.parse(e);
      var enc1key = pakde.diffieHellman(false, data.bobs.bob1, key1.x, key1.n);
      var enc2key = pakde.diffieHellman(false, data.bobs.bob2, key2.x, key2.n);

      var word = pakde.decrypt(data.message, enc1key, enc2key);

      var encrypt = "\nEncrypted: "+ '\n\n' + data.message;
      console.log(encrypt);
      var decrypt = "\nDecrypted: "+ '\n\n' + word;
      console.log(decrypt);
    }
  });
});
