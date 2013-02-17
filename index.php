<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Push Notification</title>
</head>

<form action="" method="POST">
<table>
<tr>
<td>Token</td><td><input type="text" name="token" size="100" /></td>
</tr>
<tr>
<td>Nazwa certyfikatu:</td><td><input type="text" name="nazwaCertyfikatu" size="15" /></td>
</tr>
<tr>
<td>Hasło certyfikatu:</td><td><input type="text" name="hasloCertyfikatu" size="15" /></td>
</tr>
<tr>
<td>Wiadomość:</td><td><input type="text" name="wiadomosc" size="100" /></td></tr>
<tr>
<td></td>
<td><input type="submit" value="Wyślij" /></td></tr>


</table>


</form>




<body>
</body>
</html>
<?php 
if ((isset($_POST["token"]) && !empty($_POST["token"])) 
&& (isset($_POST["wiadomosc"]) && !empty($_POST["wiadomosc"]))) {
	$token = $_POST["token"];
	$wiadomosc = $_POST["wiadomosc"];
	$hasloCertyfikatu = $_POST["hasloCertyfikatu"];
	$nazwaCertyfikatu = $_POST["nazwaCertyfikatu"];
	
	
	
$scc = stream_context_create();
stream_context_set_option($scc, 'ssl', 'local_cert', $nazwaCertyfikatu);
stream_context_set_option($scc, 'ssl', 'passphrase', $hasloCertyfikatu);

// Otwieramy połączenie do serwera APNS (wersja dla developerów, wersja produkcyjna -> gateway.push.apple.com:2195)
$apns = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $error,
	$errorstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $scc);

if (!$apns)
	exit("Błąd połączenia: $error $errorstr" . PHP_EOL);

echo 'Połączono do APNS >>> ' . PHP_EOL;

// rodzaj notyfikacji
$body['aps'] = array(
	'alert' => $wiadomosc,
	'sound' => 'default'
	);

// kodowanie JSON
$payload = json_encode($body);

// binarnie
$msg = chr(0) . pack('n', 32) . pack('H*', $token) . pack('n', strlen($payload)) . $payload;

// wyślij do serwera
$result = fwrite($apns, $msg, strlen($msg));

if (!$result)
	echo 'Wiadomość nie dostarczona...' . PHP_EOL;
else
	echo 'Wiadomość dostarczono' . PHP_EOL;

// zamknij połaczenie z serwerem
fclose($apns);
	
} else {
	echo 'Wypełnij wszystkie pola!';
}


?>