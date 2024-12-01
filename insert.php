<?php
session_start();
require 'connect.php'; // Подключение к базе данных

// Проверка на авторизацию
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}
$id_user = $_SESSION['id_user']; // Предполагается, что id_user хранится в сессии

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = trim($_POST['name']);
    $context = trim($_POST['context']);
    
    // Получаем ID категории
    $category_id = intval($_POST['kategoria']); // Убедитесь, что используете правильное имя поля
    
    // Проверка на корректность значения категории
    if ($category_id <= 0) {
        echo json_encode(['message' => 'Пожалуйста, выберите корректную категорию.']);
        exit();
    }

    $date = date('Y-m-d'); 
    $status = 'new'; // Устанавливаем начальный статус как 'new'

    // Обработка загрузки файла
    $file_name = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $file_name = basename($_FILES['file']['name']);
        $target_dir = 'uploads/'; // Папка для загрузки файлов
        $target_file = $target_dir . $file_name;

        // Перемещение загруженного файла в папку
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            echo json_encode(['message' => 'Ошибка при загрузке файла.']);
            exit();
        }
    } else {
        echo json_encode(['message' => 'Файл не был загружен или произошла ошибка при загрузке.']);
        exit();
    }
    $reason_rejection = isset($_POST['reason']) ? trim($_POST['reason']) : null;
    // Подготовка SQL-запроса для вставки заявки
    $stmt = $mysqli->prepare("INSERT INTO zayvki (id_user, name, context, category_id, date, status, file, reason_rejection) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        echo json_encode(['message' => 'Ошибка в подготовке запроса: ' . htmlspecialchars($mysqli->error)]);
        exit();
    }

    // Привязываем параметры и выполняем запрос
    if ($stmt->bind_param("isssssss", $id_user, $name, $context, $category_id, $date, $status, $file_name, $reason_rejection) && $stmt->execute()) {
        header("Location: index1.php");
        exit();
    } else {
        echo json_encode(['message' => 'Ошибка при создании заявки: ' . htmlspecialchars($stmt->error)]);
        exit;
    }
}

// Закрытие соединения и освобождение ресурсов
$stmt->close();
$mysqli->close();
?>