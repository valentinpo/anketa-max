// Инициализация MAX WebApp
const maxApp = window.MaxWebApp || window.TelegramWebApp; // Фоллбэк для тестов
let isInitialized = false;

document.addEventListener('DOMContentLoaded', () => {
    initMaxApp();
    setupForm();
});

function initMaxApp() {
    if (!maxApp) {
        console.warn('MAX WebApp SDK не найден, работаем в обычном режиме');
        return;
    }

    maxApp.ready();
    isInitialized = true;

    // Разворачиваем на весь экран
    maxApp.expand();

    // Настраиваем Main Button (главная кнопка внизу мессенджера)
    maxApp.MainButton.setText('ОТПРАВИТЬ ЗАЯВКУ');
    maxApp.MainButton.setColor(getCSSVar('--button-color', '#3390ec'));
    maxApp.MainButton.setTextColor(getCSSVar('--button-text', '#ffffff'));
    maxApp.MainButton.onClick(() => {
        document.getElementById('surveyForm').requestSubmit();
    });

    // Обновляем прогресс-бар
    updateProgress();
}

function setupForm() {
    const form = document.getElementById('surveyForm');
    const inputs = form.querySelectorAll('input, textarea');

    // Валидация и активация MainButton
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            validateForm();
            updateProgress();
        });
        input.addEventListener('change', () => {
            validateForm();
            updateProgress();
        });
    });

    // Отправка формы
    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!validateForm()) return;

        // Блокируем кнопку
        if (isInitialized) {
            maxApp.MainButton.showProgress();
        }
        document.getElementById('submitBtn').disabled = true;

        // Собираем данные
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.consent = document.getElementById('consent').checked ? 'Да' : 'Нет';

        // Отправляем на сервер
        try {
            const response = await fetch('submit.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                showSuccess();
            } else {
                showError(result.message || 'Ошибка отправки');
            }
        } catch (error) {
            console.error('Ошибка:', error);
            showError('Ошибка сети. Попробуйте позже.');
        } finally {
            if (isInitialized) {
                maxApp.MainButton.hideProgress();
            }
            document.getElementById('submitBtn').disabled = false;
        }
    });
}

function validateForm() {
    const form = document.getElementById('surveyForm');
    const required = form.querySelectorAll('[required]');
    let isValid = true;

    required.forEach(field => {
        if (field.type === 'checkbox') {
            if (!field.checked) isValid = false;
        } else if (!field.value.trim()) {
            isValid = false;
        }
    });

    // Показываем/скрываем MainButton
    if (isInitialized) {
        if (isValid) {
            maxApp.MainButton.enable();
            maxApp.MainButton.show();
        } else {
            maxApp.MainButton.disable();
        }
    }

    return isValid;
}

function updateProgress() {
    const form = document.getElementById('surveyForm');
    const inputs = form.querySelectorAll('input:not([type="checkbox"]), textarea');
    const filled = Array.from(inputs).filter(i => i.value.trim()).length;
    const progress = (filled / inputs.length) * 100;

    document.getElementById('progress').style.width = `${progress}%`;
}

function showSuccess() {
    document.getElementById('surveyForm').style.display = 'none';
    document.getElementById('successScreen').style.display = 'block';

    if (isInitialized) {
        maxApp.MainButton.hide();
        // Вибрация при успехе
        if (maxApp.HapticFeedback) {
            maxApp.HapticFeedback.notificationOccurred('success');
        }
    }
}

function showError(message) {
    alert(message);
    if (isInitialized && maxApp.HapticFeedback) {
        maxApp.HapticFeedback.notificationOccurred('error');
    }
}

function closeApp() {
    if (isInitialized) {
        maxApp.close();
    } else {
        window.close();
    }
}

// Утилита для получения CSS-переменных
function getCSSVar(name, fallback) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim() || fallback;
}