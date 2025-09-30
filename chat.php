<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD']==='OPTIONS') exit;
header("Content-Type: text/plain; charset=utf-8");

$API_KEY = "sk-or-v1-d5c92324-f979-494b-bacb-b36683242fab";
$msg = trim($_POST['msg'] ?? $_GET['msg'] ?? '');
if ($msg==='') { echo "ไม่มีข้อความ"; exit; }

$payload = [
  "model" => "meta-llama/llama-3.1-8b-instruct",
  "messages" => [
    ["role"=>"system","content"=>"ตอบไทย เป็นกันเอง"],
    ["role"=>"user","content"=>$msg],
  ],
];

$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer $API_KEY",
  ],
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_TIMEOUT => 30,
]);
$res = curl_exec($ch);
if ($res === false) { echo "curl error: ".curl_error($ch); exit; }
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code >= 300) { echo "HTTP $code\n$res"; exit; }
$j = json_decode($res, true);
echo $j['choices'][0]['message']['content'] ?? "ไม่มีคำตอบจาก AI";
