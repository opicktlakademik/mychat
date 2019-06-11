
$('.list-click').click(function(){
  var id_user = $(this).attr('data-id');
  console.log(id_user);
  var pakde = new PakdeEnrcyption;
  var key1 = pakde.diffieHellman();
  console.log(key1);
  //get the message box only
  //console.log(key1);
  $.get('Chat/getMessageBox', {getBox:'Ok..', id_user:id_user, key1:key1}, function(e, success){
    if (success && e != false) {
      $('#chat-box').empty();
      //var data = JSON.parse(e);
      $('#chat-box').append(e);

    }else {
      console.log("Something wrong success = "+success+" e = "+e);
    }
  });
});
