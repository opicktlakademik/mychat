<?php $var = null; ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Only For Testing Purpose</title>
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <script type="text/javascript" src="<?php echo base_url()?>assets/bower_components/jquery/dist/jquery.js">
    </script>
    <script type="text/javascript" src="<?php echo base_url()?>assets/myjs/PakdeEncryption.js">

    </script>
  </head>
  <body>
      <h1 align='center'>For Testing Purpose</h1>
      <div align="center">
        <div class="row">
          <div class="col-sm-9">
            <div class="box">
              <div class="box-header">
                <h1 class="box-title">Test Encryption</h1>
              </div>
              <div class="box-body" id="id-box">
                <?php // var_dump($var) ?>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        $(document).ready(function(){
          var Encryption = new PakdeEnrcyption();
          var hasil = Encryption.encrypt();
          $('#id-box').append(hasil.packing.dict1);
          '<?php $var = ''  ?>' +hasil;
        //  console.log(hasil);
        });
      </script>
  </body>
</html>
