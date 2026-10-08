<?php
// === НАСТРОЙКИ ===
$admin_email = 'admin@example.com'; // 🔴  ПОЧТА ОЛЬГИ
$db_file = 'responses.json';       // Файл для хранения заявок
// =================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Сбор и очистка данных
$data = [
    'id' => uniqid(),
    'date' => date('Y-m-d H:i:s'),
    'organization' => trim($_POST['organization'] ?? ''),
    'address' => trim($_POST['address'] ?? ''),
    'inn' => trim($_POST['inn'] ?? ''),
    'contact_person' => trim($_POST['contact_person'] ?? ''),
    'phone' => trim($_POST['phone'] ?? ''),
    'messenger' => trim($_POST['messenger'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'q1' => trim($_POST['q1'] ?? ''),
    'q2' => trim($_POST['q2'] ?? ''),
    'q3' => trim($_POST['q3'] ?? ''),
    'q4' => trim($_POST['q4'] ?? ''),
    'consent' => isset($_POST['consent']) ? 'Да' : 'Нет',
    'ip' => $_SERVER['REMOTE_ADDR']
];

// Простая валидация
if (empty($data['organization']) || empty($data['phone']) || empty($data['email'])) {
    header('Location: index.php?status=error');
    exit;
}

// 1. Сохранение в JSON-файл (база данных)
$existing = [];
if (file_exists($db_file)) {
    $content = file_get_contents($db_file);
    if ($content) {
        $existing = json_decode($content, true) ?? [];
    }
}
$existing[] = $data;
file_put_contents($db_file, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// 2. Отправка письма Ольге
$subject = "📩 Новая заявка от {$data['organization']}";
$message = "
<html>
<head><style>body{font-family:Arial;} .label{color:#666;}</style></head>
<body>
<h2>Новая анкета</h2>
<table cellpadding='10'>
    <tr><td class='label'>Организация:</td><td><b>{$data['organization']}</b></td></tr>
    <tr><td class='label'>Адрес:</td><td>{$data['address']}</td></tr>
    <tr><td class='label'>ИНН:</td><td>{$data['inn']}</td></tr>
    <tr><td class='label'>Контакт:</td><td>{$data['contact_person']}</td></tr>
    <tr><td class='label'>Телефон:</td><td><a href='tel:{$data['phone']}'>{$data['phone']}</a></td></tr>
    <tr><td class='label'>Мессенджер:</td><td>{$data['messenger']}</td></tr>
    <tr><td class='label'>Email:</td><td><a href='mailto:{$data['email']}'>{$data['email']}</a></td></tr>
    <tr><td class='label'>Вопрос 1:</td><td>{$data['q1']}</td></tr>
    <tr><td class='label'>Вопрос 2:</td><td>{$data['q2']}</td></tr>
    <tr><td class='label'>Вопрос 3:</td><td>{$data['q3']}</td></tr>
    <tr><td class='label'>Вопрос 4:</td><td>{$data['q4']}</td></tr>
    <tr><td class='label'>Согласие:</td><td>{$data['consent']}</td></tr>
    <tr><td class='label'>Дата:</td><td>{$data['date']}</td></tr>
</table>
</body>
</html>
";

$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: Анкета <no-reply@yourdomain.ru>\r\n";

@mail($admin_email, $subject, $message, $headers);

// Переадресация
header('Location: index.php?status=success');
exit;
?>