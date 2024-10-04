<?php
$curl = curl_init('https://api.github.com');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
if ($response === false) {
    echo 'Curl error: ' . curl_error($curl);
} else {
    echo 'Operation completed without any errors';
}
curl_close($curl);
