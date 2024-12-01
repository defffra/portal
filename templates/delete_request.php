<?php
session_start();

// Establish a database connection
$conn = new mysqli('localhost', 'root', '', 'portal');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверяем, что пользователь аутентифицирован
    if (!isset($_SESSION['id_user'])) {
        header("Location: index.php"); 
        exit();
    }

    // Получаем ID заявки из POST-запроса
    if (isset($_POST['request_id'])) {
        $request_id = intval($_POST['request_id']);

        // Подготовка SQL-запроса для удаления заявки
        $stmt = $conn->prepare("DELETE FROM zayvki WHERE id = ? AND id_user = ?");
        
        if ($stmt === false) {
            die("Ошибка в подготовке запроса: " . $conn->error);
        }

        $id_user = intval($_SESSION['id_user']);
        
        // Привязываем параметры и выполняем запрос
        $stmt->bind_param("ii", $request_id, $id_user);
        
        if ($stmt->execute()) {
            header("Location: ../index1.php"); // Перенаправление обратно в личный кабинет после удаления
            exit();
        } else {
            die("Ошибка при удалении заявки: " . $stmt->error);
        }

        $stmt->close(); // Закрываем подготовленный запрос
    }
}

$conn->close(); // Закрываем соединение с базой данных
?>