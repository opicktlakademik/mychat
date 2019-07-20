
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
      $('#chat-box1').empty();
      $('#chat-box1').append(data.page);
      $('h3#recipient-name').empty();
      $('h3#recipient-name').html(data.recipient);
      $('input[name=recipient_id]').val(id_user);
      $('input[name=recipient_name]').val(data.recipient);
      $('.chat').empty();
      $('#recipient_id').html(data.recipient);
      var i = 0;
      for (i; i < data.message.length; i++) {
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
        console.log("Pesan encrypted: \n"+data.message[i].pesan+"\n\nPesan decrypted: \n"+message);
        showChatBox(message, data.message[i].waktu, name, possition1, possition2, data.message[i].pesan.toString(), i);
        //$('#'+i).html(data.message[i].pesan);
      }

    }
  });
});
function showChatBox(message, time = null, name = null, possition1 = null, possition2 = null, encrypt = null, i) {
  var cipertext = encrypt;
  var content = `
    <div class='item' id="item`+i+`">
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

        <p class='encrypted' id="enc`+i+`">
          <span class='text-muted'>  `+encrypt+` </span>
        </p>
      </div>
      <!-- /.attachment -->
    </div><span id="span`+i+`"></span>
    `;
  $('.chat').append(content);
  var last = $('.item').last().attr('id');
  var elmnt = document.getElementById(last);
  elmnt.scrollIntoView();
}

function sendMessage()
{
  var message = $('#input_message').val().trim();
  if (message) {
    $('#input_message').val('');
    var properties = pakde(0);
    $.get('Chat/ackReq', {key1:properties.key1_to_send, key2:properties.key2_to_send}, function(e, success){
      if (success) {
        var data = JSON.parse(e);
        var enc1key = pakde(2, {'bob':data.key1.bob,'x':properties.key1.x,'n':properties.key1.n});
        var enc2key = pakde(2, {'bob':data.key2.bob,'x':properties.key2.x,'n':properties.key2.n});
        var encmsg = pakde(1, {'message':message, 'enc1key':enc1key, 'enc2key':enc2key});
        console.log(data);
        console.log(enc1key);
        console.log(enc2key);
        var date = new Date();
        var month = date.getMonth()+1;
        var dateString = date.getFullYear()+"-"+month+"-"+date.getDate()+" "+date.getHours()+":"+date.getMinutes()+":"+date.getSeconds();
        var i = parseInt($('div.item').length);
        //console.log(i);
        showChatBox(message, dateString, $('span#user_name').html(), "right", "left", encmsg, i);
        $.post('Chat/inputMessage', {'msg':encmsg, 'user':$('input[name=recipient_id]').val(), 'date':dateString}, function(e, success){
          if (success) {
            var data = JSON.parse(e);
            if (!e.status) {
              console.log(data);
            }else {
              console.log(data);
            }
          }
        });

      }
    });
  }
}

function pakde(act = 0, _prop = null)
{
  var pakde = new PakdeEnrcyption;;
  switch (act) {
    case 0:
      var key1 = pakde.diffieHellman(true);
      var key2 = pakde.diffieHellman(true);
      var key1_to_send = {'n':key1.n, 'g':key1.g, 'alice':key1.alice};
      var key2_to_send = {'n':key2.n, 'g':key2.g, 'alice':key2.alice};
      return {key1, key2, key1_to_send, key2_to_send};
      break;
    case 1:
      return pakde.encrypt(_prop.message, _prop.enc1key, _prop.enc2key);
      break;
    case 2:
      return pakde.diffieHellman(false, _prop.bob, _prop.x, _prop.n);
      break;
    default:

  }
}
