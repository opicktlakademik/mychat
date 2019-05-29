<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Only For Testing Purpose</title>
    <script type="text/javascript" src="<?php echo base_url()?>assets/bower_components/jquery/dist/jquery.js">
    </script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/myjs/PakdeEncryption.js">

    </script>
  </head>
  <body>
      <h1 align='center'>For Testing Puropose</h1>
      <script type="text/javascript">
        $(document).ready(function(){
          var Encryption = new PakdeEnrcyption();
          console.log(Encryption.encrypt());
        });
      </script>
  </body>
</html>
