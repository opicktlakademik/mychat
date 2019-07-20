<div class="box box-primary box-solid">
  <div class="box-header with-border">
    <i class="fa fa-comments-o"></i>

    <h3 class="box-title" id="recipient-name">Chat</h3>
  </div>
  <div class="" style=" width: auto; height: 425px;">
    <div class="box-body chat" style="overflow-y: scroll; overflow: auto; width: auto; height: 100%;" id="chat-box">


    </div>
  </div>
  <!-- /.chat -->
  <div class="box-footer">
    <div class="input-group">
      <input class="form-control" placeholder="Type message..." autocomplete="off" name="message" id="input_message">
      <input type="hidden" name="recipient_id" value="">
      <input type="hidden" name="recipient_name" value="">
      <div class="input-group-btn">
        <button title="Send Message" onclick="sendMessage()"  type="button" class="btn btn-success"><i class="fa fa-send"></i></button>
      </div>
    </div>
  </div>
</div>
<!-- SlimScroll -->
<script src="<?php echo base_url(); ?>assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script type="text/javascript">
$('input[name=message]').keypress(function ( event ) {
  if (event.which == 13) {
    var message = $('input[name=message]').val();
    if (message) {
      sendMessage();
    }else {
      console.log("input empty");
    }
  }
})
</script>
