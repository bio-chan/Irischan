<?php
// อนุญาต CORS (ถ้าไฟล์อยู่คนละโดเมน)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; } // preflight

// ======= ตั้ง API KEY =======
$OPENAI_API_KEY = "d5c92324-f979-494b-bacb-b36683242fab";

// รับข้อความจาก GET/POST
$userMsg = '';
if (isset($_POST['msg'])) $userMsg = $_POST['msg'];
elseif (isset($_GET['msg'])) $userMsg = $_GET['msg'];

$userMsg = trim((string)$userMsg);
if ($userMsg === '') { echo "ไม่มีข้อความ"; exit; }

// เรียก OpenAI (ปรับรุ่นได้)
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer $OPENAI_API_KEY",
  ],
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode([
    "model" => "gpt-4o-mini",
    "messages" => [
      ["role" => "system", "content" => "คุณคือผู้ช่วยที่ตอบเป็นภาษาไทย น้ำเสียงเป็นกันเอง น่ารัก"],
      ["role" => "user", "content" => $userMsg]
    ]]
  )
]);
$res = curl_exec($ch);
if ($res === false) { echo "เรียก API ไม่ได้"; exit; }
curl_close($ch);

$j = json_decode($res, true);
echo $j['choices'][0]['message']['content'] ?? "ไม่มีคำตอบจาก AI";

