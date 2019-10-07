<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
	<meta charset="utf-8">
	<title>Test Encryption</title>
	<style>
		thead {
			word-break: break-word;
		}
	</style>
	<script src="<?php echo base_url('assets\bower_components\jquery\dist\jquery.min.js') ?>"></script>
	<script src="<?php echo base_url('assets\myjs\big.min.js') ?> "></script>
	<script src="<?php echo base_url('assets\myjs\bignumber.min.js') ?> "></script>
	<script src="<?php echo base_url('assets\myjs\PakdeEncryption.js') ?>"></script>
</head>

<body>
	<div align="center">
		<form class="" action=" <?php echo site_url('Pengujian/testphp') ?> " method="post">
			<label for="">Word</label> <br>
			<textarea name="text" rows="10" cols="90"><?php echo (isset($text)) ? $text : ""  ?></textarea> <br>
			<input type="submit" name="submit" value="Submit">
		</form>
		<div style="max-width:600px;">

			<table border="1">
				<thead>
					<tr>
						<th>Caesar Cipher Client Side</th>
					</tr>
					<tr>
						<td id="cc"><?php echo (isset($cc)) ? $cc : "It will be the caesar cipher encryption"  ?></td>
					</tr>
				</thead>
			</table>

			<table border="1">
				<thead>
					<tr>
						<th>Matrix Cipher Client Side</th>
					</tr>
					<tr>
						<td id="mc"><?php echo (isset($mc)) ? $mc : "It will be the matrix cipher encryption"  ?></td>
					</tr>
				</thead>
			</table>
			<table border="1">
				<thead style="word">
					<tr>
						<th>Packaged</th>
					</tr>
					<tr>
						<td id="pkg"><?php echo (isset($pkg)) ? $pkg : "It will be the packaged result"  ?></td>
					</tr>
				</thead>
			</table>
			<table border="1">
				<thead>
					<tr>
						<th>Decrypted From Server</th>
					</tr>
					<tr>
						<td id="upkg"><?php echo (isset($upkg)) ? $upkg : "It will be the decrypted result" ?></td>
					</tr>
				</thead>
			</table>
			<!--  <label for="">Unpacking</label>
        <textarea name="name" rows="8" cols="80"><?php echo $word4 ?> </textarea>
        <p style="max-width:720px;">
          Packing <br>
          <?php echo $reportpack; ?>
        </p>
        <p style="max-width:720px;">
          Unpacking <br> <?php echo $reportunpack ?>
        </p>-->
		</div>
	</div>
	<script>
		$(function() {
			var pakde = new PakdeEnrcyption;
			//$('td').html(pakde.encrypt_test().packing);

		})
		$('form').on('submit', function(e) {
			e.preventDefault();
			var url = '<?php echo site_url('Pengujian/akg') ?>';
			var pakde = new PakdeEnrcyption;
			var key1 = pakde.diffieHellman(true);
			var key2 = pakde.diffieHellman(true);
			var key1_to_send = {
				'n': key1.n,
				'g': key1.g,
				'alice': key1.alice
			};
			var key2_to_send = {
				'n': key2.n,
				'g': key2.g,
				'alice': key2.alice
			};
			var text = $('textarea[name=text]').val();
			$.get(url, {
				key1: key1_to_send,
				key2: key2_to_send,
				text: text
			}, function(e) {
				var serverBob = JSON.parse(e)
				console.log(serverBob);
				var alice_key1 = pakde.diffieHellman(false, serverBob.bob1, key1.x, key1.n);
				var alice_key2 = pakde.diffieHellman(false, serverBob.bob2, key2.x, key2.n);
				console.log(alice_key1);
				console.log(alice_key2);
				var textEncrypted = pakde.ujicoba1(text, parseInt(alice_key1), parseInt(alice_key2));
				console.log(textEncrypted);
				$('#cc').html(textEncrypted.cc);
				$('#mc').html(textEncrypted.mc);
				$('#pkg').html(textEncrypted.pkg);
				var urlPost = ''
				$.post('<?php echo site_url('Pengujian/testphp') ?>', {
					text: textEncrypted.pkg
				}, function(response) {
					var res = JSON.parse(response);
					$('#upkg').html(res.upkg);
					console.log(res);
					console.log(key1);
					console.log(key2);
					console.log(alice_key1);
					console.log(alice_key2);




				})
			})
		})
	</script>
</body>


</html>
