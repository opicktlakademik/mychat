
$('.list-click').click(function(){
  var id_user = $(this).attr('data-id');
  var pakde = new PakdeEnrcyption;
  var key1 = pakde.diffieHellman(true);
  var key2 = pakde.diffieHellman(true);
  var key1_to_send = {'n':key1.n, 'g':key1.g, 'alice':key1.alice};
  var key2_to_send = {'n':key2.n, 'g':key2.g, 'alice':key2.alice};
  console.log('Menyiapkan properti diffieHellman [x, g, n, alice]');
  console.log("P1: "+ JSON.stringify(key1));
  console.log("P2: "+ JSON.stringify(key2));
  //request to server
  console.log('\nMengirim request get dengan parameter id penerima, properti 1 dan 2 [n, g , alice]');
  console.log('ID Penerima: ' + id_user + '\np1: '+ JSON.stringify(key1_to_send) + '\np2: ' + JSON.stringify(key2_to_send));
  $.get('Chat/getMessageBox', {id_user:id_user, key1:key1_to_send, key2:key2_to_send}, function(e, success){
    if (success) {

      var data = JSON.parse(e);
      var enc1key = pakde.diffieHellman(false, data.key1.bob, key1.x, key1.n);
      var enc2key = pakde.diffieHellman(false, data.key2.bob, key2.x, key2.n);

      console.log("\nMenerima response dari server: \n\n"+JSON.stringify(data));
      console.log("\nGenerate kunci pergeseran\n"+"\nKunci 1: " + enc1key + "\nKunci 2: "+enc2key+"\n");
      console.log("\nMelakukan pendekripsian pesan\n");
      // the decrypt function -> pakde.decrypt(data.message[i].pesan, enc1key, enc2key )
      $('#chat-title').empty();
      $('#chat-title').append(data.receiver);
      $('#chat-box1').empty();
      $('#chat-box1').append(data.page);
      $('#disinichat').empty();
      $('#recipient').html(data.recipient);
      for (var i = 0; i < data.message.length; i++) {
        var message = pakde.decrypt(data.message[i].pesan, enc1key, enc2key );
        var possition1 = "left";
        var possition2 = "right";
        var name = '';
        if (data.message[i].id_user1 === data.sender_id) {
          possition1 = "right";
          possition2 = "left";
          name = data.sender;
        }else {
          name = data.recipient;
        }
        console.log("Pesan encrypted: \n"+data.message[i].pesan+"\n\nPesan ecryptd: \n"+message);
        showChatBox(message, data.message[i].waktu, name, possition1, possition2, data.message[i].pesan.toString(), i);
        $('#'+i).html(data.message[i].pesan);
      }
    }
  });
});
function showChatBox(message, time, name, possition1, possition2, encrypt, i) {
  var cipertext = "<p> "+encrypt+"</p>";
  var content = `
    <div class='item'>
      <br><br><br>

      <p class='message'>
        <a href='#' class='name'>
          <small class='text-muted pull-`+possition2+`'><i class='fa fa-clock-o'></i> `+time+`</small>
          <text class="pull-`+possition1+`">`+name+`</text>
        </a>
      </p>
      <br>
      <div class='attachment'>
        <h4>Message: </h4>
        <p class='encrypted'>
          <p style="font-weight:normal;">`+message+`</p>
        </p><br>
        <h4>Decrypted:</h4>

        <p class='encrypted'>
          <span class='text-muted' id="`+i+`">  `+" "+` </span>
        </p>
      </div>
      <!-- /.attachment -->
    </div>
    `;
  $('#disinichat').append(content);
}
