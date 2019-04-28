$('.list-click').click(function(){
  var id_user = $(this).attr('data-id');
  console.log(id_user);
  //get the message box only
  $.get('Chat/getMessageBox', {getBox:'Ok..', id_user:id_user}, function(e, success){
    if (success && e != false) {
      $('#chat-box').empty();
      var data = JSON.parse(e);

      //key compromizing

    }else {
      console.log("Something wrong success = "+success+" e = "+e);
    }
  });
});
