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
	<div align="center" id="center">


	</div>
	<script>
		$(function() {
			var url = '<?php echo site_url('Pengujian/getText') ?>';
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
			$.get(url, {
				key1: key1_to_send,
				key2: key2_to_send
			}, function(e) {
				//hitung kunci
				var data = JSON.parse(e);
				var messages = data.messages;
				var alice_key1 = pakde.diffieHellman(false, data.bob1, key1.x, key1.n);
				var alice_key2 = pakde.diffieHellman(false, data.bob2, key2.x, key2.n);
				$('#center').append("Client Properties 1: " + JSON.stringify(key1) + "the_key:" + alice_key1 + "<br>");
				$('#center').append("Client Properties 2: " + JSON.stringify(key2) + "the_key:" + alice_key2 + "<br>");
				$('#center').append("Server Properties 1: " + JSON.stringify(data.properties.key1) + "<br>");
				$('#center').append("Server Properties 2: " + JSON.stringify(data.properties.key2) + "<br>");
				$('#center').append("<br><br>Dict1public: " + JSON.stringify(data.dictpublic) + "<br>");
				$('#center').append("<br><br>Dict1: " + "<br>");
				for (let index = 0; index < data.dict1.length; index++) {
					$('#center').append(data.dict1[index]);

				}
				$('#center').append('<br><br>' + "Dict2: " + "<br>");
				for (let j = 0; j < data.dict2.length; j++) {
					for (let k = 0; k < data.dict2[j].length; k++) {
						$('#center').append(data.dict2[j][k] + ", ");

					}
					$('#center').append('<br>');
				}
				$('#center').append('<br><br>' + "Dict1caesar: <br>" + JSON.stringify(data.dict1caesar) + "<br>");
				$('#center').append('<br><br>' + "Dict1matrix: <br>" + JSON.stringify(data.dict1matrix) + "<br>");
				$('#center').append('<br><br>' + "Dict2caesar: <br>" + JSON.stringify(data.dict2caesar) + "<br>");
				$('#center').append('<br><br>' + "Dict2matrix: <br>" + JSON.stringify(data.dict2matrix) + "<br>");

				for (let i = 0; i < messages.length; i++) {
					var decrypted = pakde.decrypt(messages[i].pkg, alice_key1, alice_key2);
					tampilPesan(messages[i].cc, messages[i].mc, messages[i].pkg, decrypted, i + 1);
					console.log(decrypted);

				}
			});

		});

		function tampilPesan(cc, mc, pkg, dec, i) {
			var divToShow = `<br><br><div style="max-width:600px;">

			<table border="1">
				<thead>
					<tr>
						<th id="thcc">Text Encryptred By Caesar Cipher From Server Side Sample ` + i + `</th>
					</tr>
					<tr>
						<td id="cc">` + cc + `</td>
					</tr>
				</thead>
			</table>
			<br>
			<table border="1">
				<thead>
					<tr>
						<th>Text Encryptred By Matrix Cipher Server Side Sample ` + i + `</th>
					</tr>
					<tr>
						<td id="mc">` + mc + `</td>
					</tr>
				</thead>
			</table>
			<br>
			<table border="1">
				<thead style="word">
					<tr>
						<th>Packaged By Server Sample ` + i + `</th>
					</tr>
					<tr>
						<td id="pkg">` + pkg + ` </td>
					</tr>
				</thead>
			</table>
			<br>
			<table border="1">
				<thead>
					<tr>
						<th>Decrypted By Client Sample ` + i + `</th>
					</tr>
					<tr>
						<td id="upkg">` + dec + `</td>
					</tr>
				</thead>
			</table>
		</div><br><hr size="1"><br>`;
			$('#center').append(divToShow);

		}


		//separated
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
