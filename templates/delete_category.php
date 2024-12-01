<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php';  

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']); // Получаем ID категории

    // Подготовка SQL-запроса для удаления категории
    $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $category_id); // Привязываем параметры
        if ($stmt->execute()) {
            // Успешное удаление
            header("Location: admin.php");
            exit();
        } else {
            // Ошибка при выполнении запроса
            echo "Ошибка при удалении категории: " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    } else {
        echo "Ошибка подготовки запроса: " . htmlspecialchars($mysqli->error);
    }
} else {
    echo "Некорректный запрос.";
}

$mysqli->close();
?>