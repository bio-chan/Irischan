<?php
// ใส่ API key ของคุณ
$apiKey = '8315efeb-5691-4bdb-a7b3-9469e5b0a6aa';  // <-- ใส่คีย์ OpenAI ของคุณ

$msg = $_POST['msg'] ?? '';

if(!$msg){ echo 'ไม่มีข้อความ'; exit; }

// เรียก OpenAI GPT-4 (หรือ GPT-3.5)
// ต้องเปิด allow_url_fopen หรือใช้ cURL
$ch = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer '.$apiKey
]);

$data = [
  'model' => 'gpt-3.5-turbo',
  'messages' => [
    ['role' => 'system', 'content' => 'คุณคือผู้ช่วยตอบแบบเป็นกันเอง ภาษาไทย'],
    ['role' => 'user', 'content' => $msg]
  ]
];

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$result = curl_exec($ch);
if($result===false){ echo 'เรียก API ไม่ได้'; exit; }
curl_close($ch);

$json = json_decode($result,true);
echo $json['choices'][0]['message']['content'] ?? 'ไม่มีคำตอบจาก AI';
