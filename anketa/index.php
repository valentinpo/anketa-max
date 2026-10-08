<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета организации</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1>Анкета организации</h1>
            <p class="subtitle">Заполните форму, и мы свяжемся с вами</p>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
                <div class="alert success">✅ Спасибо! Ваша заявка отправлена.</div>
            <?php endif; ?>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
                <div class="alert error">❌ Ошибка отправки. Попробуйте позже.</div>
            <?php endif; ?>

            <form action="submit.php" method="POST">
                <div class="form-group">
                    <label for="organization">Организация *</label>
                    <input type="text" id="organization" name="organization" required placeholder="ООО «Ромашка»">
                </div>

                <div class="form-group">
                    <label for="address">Адрес *</label>
                    <input type="text" id="address" name="address" required placeholder="г. Москва, ул. Примерная, д. 1">
                </div>

                <div class="form-group">
                    <label for="inn">ИНН *</label>
                    <input type="text" id="inn" name="inn" required placeholder="1234567890" pattern="[0-9]{10,12}">
                </div>

                <div class="form-group">
                    <label for="contact_person">Контактное лицо *</label>
                    <input type="text" id="contact_person" name="contact_person" required placeholder="Иванов Иван">
                </div>

                <div class="form-group">
                    <label for="phone">Телефон *</label>
                    <input type="tel" id="phone" name="phone" required placeholder="+7 (999) 000-00-00">
                </div>

                <div class="form-group">
                    <label for="messenger">Мессенджер</label>
                    <input type="text" id="messenger" name="messenger" placeholder="Telegram / WhatsApp">
                </div>

                <div class="form-group">
                    <label for="email">Электронная почта *</label>
                    <input type="email" id="email" name="email" required placeholder="info@company.ru">
                </div>

                <hr class="divider">
                <h3>Дополнительные вопросы</h3>

                <div class="form-group">
                    <label for="q1">Вопрос 1</label>
                    <textarea id="q1" name="q1" rows="3" placeholder="Ваш ответ..."></textarea>
                </div>

                <div class="form-group">
                    <label for="q2">Вопрос 2</label>
                    <textarea id="q2" name="q2" rows="3" placeholder="Ваш ответ..."></textarea>
                </div>

                <div class="form-group">
                    <label for="q3">Вопрос 3</label>
                    <textarea id="q3" name="q3" rows="3" placeholder="Ваш ответ..."></textarea>
                </div>

                <div class="form-group">
                    <label for="q4">Вопрос 4</label>
                    <textarea id="q4" name="q4" rows="3" placeholder="Ваш ответ..."></textarea>
                </div>

                <div class="form-group checkbox-group">
                    <input type="checkbox" id="consent" name="consent" required>
                    <label for="consent">Я согласен на обработку персональных данных *</label>
                </div>

                <button type="submit" class="submit-btn">Отправить заявку</button>
            </form>
        </div>
    </div>
</body>
</html>