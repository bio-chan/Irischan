<?php
// ===== Debug & Headers (ช่วยกันจอขาว) =====
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }
header("Content-Type: text/plain; charset=utf-8");

// ======= ตั้งค่าโปรVIDER =======
$PROVIDER = 'groq';  // 'groq' (แนะนำ) หรือ 'openrouter'

// ======= ใส่ API KEY ของคุณ =======
$GROQ_API_KEY      = 'd5c92324-f979-494b-bacb-b36683242fab';
$OPENROUTER_API_KEY= 'd5c92324-f979-494b-bacb-b36683242fab';

// ===== รับข้อความผู้ใช้ =====
$msg = trim($_POST['msg'] ?? $_GET['msg'] ?? '');
if ($msg==='') { echo "ไม่มีข้อความ"; exit; }

// ===== เตรียม payload มาตรฐาน =====
$systemPrompt = "คุณคือผู้ช่วย AI ตอบเป็นภาษาไทย น้ำเสียงเป็นกันเอง น่ารัก สุภาพ และกระชับ";
$payload = [
  "messages" => [
    ["role"=>"system","content"=>$systemPrompt],
    ["role"=>"user","content"=>$msg],
  ],
  "temperature" => 0.7
];

if ($PROVIDER === 'groq') {
  $endpoint = "https://api.groq.com/openai/v1/chat/completions";
  $payload["model"] = "llama-3.1-8b-instant"; // หรือ 'llama-3.1-70b-versatile'
  $headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $GROQ_API_KEY",
    "User-Agent: pink-llama-php/1.0"
  ];
}
else if ($PROVIDER === 'openrouter') {
  $endpoint = "https://openrouter.ai/api/v1/chat/completions";
  $payload["model"] = "meta-llama/llama-3.1-8b-instruct";
  $headers = [
    "Content-Type: application/json",
    "Authorization: Bearer $OPENROUTER_API_KEY",
    "HTTP-Referer: https://example.com",
    "X-Title: Pink LLaMA Chat",
  ];
} else {
  echo "PROVIDER ไม่ถูกต้อง"; exit;
}

// ===== เรียก API =====
$ch = curl_init($endpoint);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER     => $headers,
  CURLOPT_POST           => true,
  CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_UNICODE),
  CURLOPT_TIMEOUT        => 40,
]);
$res  = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err  = curl_error($ch);
curl_close($ch);

if ($res === false) { echo "curl error: $err"; exit; }
if ($code >= 300)  { echo "HTTP $code\n$res"; exit; }

$j = json_decode($res, true);

// โครงตอบกลับของทั้ง Groq และ OpenRouter เหมือน OpenAI
$out = $j['choices'][0]['message']['content'] ?? '';
echo $out !== '' ? $out : "ไม่มีคำตอบจาก AI";

