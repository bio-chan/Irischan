<?php
// Debug & CORS
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;
header("Content-Type: text/plain; charset=utf-8");

// ====== API Key ======
$API_KEY = "sk-or-v1-d5c92324-f979-494b-bacb-b36683242fab";

// ====== รับข้อความผู้ใช้ ======
$msg = trim($_POST['msg'] ?? $_GET['msg'] ?? '');
if ($msg === '') { echo "ไม่มีข้อความ"; exit; }

// ====== เตรียม payload ======
$payload = [
  "model" => "meta-llama/llama-3.1-8b-instruct",
  "messages" => [
    ["role"=>"system","content"=>"คุณคือผู้ช่วย AI ที่ตอบเป็นภาษาไทย น้ำเสียงเป็นกันเอง น่ารัก และสุภาพ"],
    ["role"=>"user","content"=>$msg],
  ]
];

// ====== เรียก API ======
$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer $API_KEY",
    "HTTP-Referer: https://example.com", // เปลี่ยนเป็นเว็บคุณถ้ามี
    "X-Title: Pink Chat"
  ],
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT => 40,
]);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($res === false) { echo "curl error: $err"; exit; }
if ($code >= 300)  { echo "HTTP $code\n$res"; exit; }

$j = json_decode($res, true);
echo $j['choices'][0]['message']['content'] ?? "ไม่มีคำตอบจาก AI";
