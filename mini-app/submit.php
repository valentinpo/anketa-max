<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка preflight запроса
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Только POST']);
    exit;
}

// Получаем JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Некорректные данные']);
    exit;
}

// === НАСТРОЙКИ ===
$admin_email = 'olga@example.com'; // 🔴 ВПИШИ ПОЧТУ ОЛЬГИ
$db_file = '../anketa/responses.json'; // Сохраняем в ту же базу!
// =================

// Сбор данных
$data = [
    'id' => uniqid(),
    'date' => date('Y-m-d H:i:s'),
    'source' => 'max_mini_app', // Метка источника
    'organization' => trim($input['organization'] ?? ''),
    'address' => trim($input['address'] ?? ''),
    'inn' => trim($input['inn'] ?? ''),
    'contact_person' => trim($input['contact_person'] ?? ''),
    'phone' => trim($input['phone'] ?? ''),
    'messenger' => trim($input['messenger'] ?? ''),
    'email' => trim($input['email'] ?? ''),
    'q1' => trim($input['q1'] ?? ''),
    'q2' => trim($input['q2'] ?? ''),
    'q3' => trim($input['q3'] ?? ''),
    'q4' => trim($input['q4'] ?? ''),
    'consent' => ($input['consent'] === 'Да') ? 'Да' : 'Нет',
    'ip' => $_SERVER['REMOTE_ADDR'] ?? ''
];

// Валидация
if (empty($data['organization']) || empty($data['phone']) || empty($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Заполните обязательные поля']);
    exit;
}

// Сохранение в БД
$existing = [];
if (file_exists($db_file)) {
    $content = file_get_contents($db_file);
    if ($content) {
        $existing = json_decode($content, true) ?? [];
    }
}
$existing[] = $data;
file_put_contents($db_file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Отправка письма Ольге
$subject = "📩 Новая заявка (MAX Mini App) от {$data['organization']}";
$message = "
<html>
<head><style>body{font-family:Arial;} .label{color:#666;}</style></head>
<body>
<h2>Новая анкета из MAX</h2>
<table cellpadding='10'>
    <tr><td class='label'>Организация:</td><td><b>{$data['organization']}</b></td></tr>
    <tr><td class='label'>Адрес:</td><td>{$data['address']}</td></tr>
    <tr><td class='label'>ИНН:</td><td>{$data['inn']}</td></tr>
    <tr><td class='label'>Контакт:</td><td>{$data['contact_person']}</td></tr>
    <tr><td class='label'>Телефон:</td><td><a href='tel:{$data['phone']}'>{$data['phone']}</a></td></tr>
    <tr><td class='label'>Email:</td><td><a href='mailto:{$data['email']}'>{$data['email']}</a></td></tr>
    <tr><td class='label'>Источник:</td><td>MAX Mini App</td></tr>
    <tr><td class='label'>Дата:</td><td>{$data['date']}</td></tr>
</table>
</body>
</html>
";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Анкета <no-reply@skylume.ru>\r\n";

@mail($admin_email, $subject, $message, $headers);

echo json_encode(['success' => true]);
?>