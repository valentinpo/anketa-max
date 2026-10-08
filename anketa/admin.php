<?php
session_start();
// 🔴 ЗАДАЙ ПАРОЛЬ ДЛЯ ВХОДА
$admin_password = 'olga2024'; 

if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged'] = true;
    } else {
        $error = 'Неверный пароль';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Вход для администратора</title>
        <style>
            body{font-family:Arial;display:flex;justify-content:center;align-items:center;height:100vh;background:#f5f5f5;}
            .login-box{background:white;padding:40px;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
            input{padding:12px;width:100%;margin:10px 0;border:1px solid #ddd;border-radius:6px;}
            button{width:100%;padding:12px;background:#3390ec;color:white;border:none;border-radius:6px;cursor:pointer;}
        </style>
    </head>
    <body>
        <div class="login-box">
            <h2>🔐 Вход</h2>
            <?php if(isset($error)): ?><p style="color:red"><?= $error ?></p><?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Войти</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$db_file = 'responses.json';
$responses = file_exists($db_file) ? json_decode(file_get_contents($db_file), true) : [];
// Сортировка: новые сверху
$responses = array_reverse($responses);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Заявки</title>
    <style>
        body{font-family:Arial;background:#f5f5f5;padding:20px;}
        .header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;}
        .table-container{background:white;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
        table{width:100%;border-collapse:collapse;}
        th,td{padding:15px;text-align:left;border-bottom:1px solid #eee;}
        th{background:#f8f9fa;font-weight:600;}
        tr:hover{background:#f5f5f5;}
        .btn{padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;}
        .btn-logout{background:#dc3545;color:white;}
        .btn-export{background:#28a745;color:white;}
        .status-new{background:#ffc107;padding:4px 8px;border-radius:4px;font-size:12px;}
        @media(max-width:768px){table{font-size:14px;} th,td{padding:10px;}}
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Заявки (<?= count($responses) ?>)</h1>
        <div>
            <a href="export.php" class="btn btn-export">📥 Экспорт в CSV</a>
            <a href="?logout=1" class="btn btn-logout">Выйти</a>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Организация</th>
                    <th>Контакт</th>
                    <th>Телефон</th>
                    <th>Email</th>
                    <th>ИНН</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($responses)): ?>
                    <tr><td colspan="6" style="text-align:center;">Заявок пока нет</td></tr>
                <?php else: ?>
                    <?php foreach ($responses as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['date']) ?></td>
                            <td><?= htmlspecialchars($r['organization']) ?></td>
                            <td><?= htmlspecialchars($r['contact_person']) ?></td>
                            <td><a href="tel:<?= htmlspecialchars($r['phone']) ?>"><?= htmlspecialchars($r['phone']) ?></a></td>
                            <td><a href="mailto:<?= htmlspecialchars($r['email']) ?>"><?= htmlspecialchars($r['email']) ?></a></td>
                            <td><?= htmlspecialchars($r['inn']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>