<?php
ini_set('display_errors',1); error_reporting(E_ALL);
header('Content-Type:text/plain; charset=utf-8');

echo "1) PHP OK\n";
echo "2) cURL: ".(function_exists('curl_init')?'YES':'NO')."\n";

$API_KEY = "sk-or-v1-d5c92324-f979-494b-bacb-b36683242fab";
$ch = curl_init("https://openrouter.ai/api/v1/models");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER=>true,
  CURLOPT_HTTPHEADER=>[
    "Authorization: Bearer $API_KEY"
  ],
  CURLOPT_TIMEOUT=>20
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

echo "3) HTTP code: $code\n";
if($err)  echo "cURL error: $err\n";
echo "4) Body (ตัด 800 ตัวอักษร):\n".substr($res??'',0,800)."\n";
