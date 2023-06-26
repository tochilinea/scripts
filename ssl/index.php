<?php
/* 
 * Takes a domain name and returns SSL certificate information
*/
 
 
function getSSL($domain_name)
{
    $errno = 0;
    $errstr = '';

    // Set socket connection timeout
    $timeout = 30;

    // Create a stream context to open a ssl socket
    // Set options to get ssl certificate
    // https://www.php.net/manual/en/context.php
    // https://www.php.net/manual/en/context.ssl.php

    $ssl_info = stream_context_create(array(
                                        "ssl" => array(
                                        "capture_peer_cert" => TRUE)));

    $stream = stream_socket_client("ssl://" . $domain_name . ":443",
                                    $errno,
                                    $errstr,
                                    $timeout, 
                                    STREAM_CLIENT_CONNECT,
                                    $ssl_info);

    if (!$stream) {
        //echo "ERROR: $errno - $errstr";
        //echo "SSL не найден. Хотите заказать SSL?";
    } else {
        $cert_resource = stream_context_get_params($stream);
        $certificate = $cert_resource['options']['ssl']['peer_certificate'];
        $certinfo = openssl_x509_parse($certificate);
        fclose($stream);
        return $certinfo;
    }
}


$certinfo = getSSL($_GET["ssl"]);


//print_r($certinfo);

//echo 'Домен: '   . $certinfo['subject']['CN'] . "\r\n";
//echo '(Выдан: '   . $certinfo['issuer']['CN'] . "\r\n)";

//echo '<br>Истекает: ' . date('d.m.Y H:i', $certinfo['validTo_time_t']);

//echo '<br><b>Истекает:</b> ' . date('d.m.Y', $certinfo['validTo_time_t']);



?>


<form action="<?php echo $_SERVER['PHP_SELF']; ?>">

<input type="text" name="ssl" required="required" placeholder="Введите домен" style="width:250px; height:37px;">
<input type="submit" value="SSL check" style="width:100px; height:37px; color:#fff; background-color: #456343;">
</form>



<?php
//print_r($certinfo);
if (isset($_GET['ssl']) != "")  {
 echo 'Домен: '   . $certinfo['subject']['CN'] . "\r\n";
 echo '(SSL выдан: '   . $certinfo['issuer']['O'] . "\r\n)";
 echo '<br>SSL истекает: ' . date('d.m.Y', $certinfo['validTo_time_t']);


} 

?>
