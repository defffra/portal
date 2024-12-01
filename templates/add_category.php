<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php");
        exit();
    }

    $category_name = trim($_POST['category_name']);
    
    $stmt = $mysqli->prepare("INSERT INTO categories (category_name) VALUES (?)");
    
    if ($stmt) {
        $stmt->bind_param("s", $category_name);
        
        if ($stmt->execute()) {
            header("Location: admin.php"); // Перенаправление обратно на страницу администратора
            exit();
        } else {
            echo "Ошибка при добавлении категории.";
        }
        
        $stmt->close();
    } else {
      echo "Ошибка в подготовке запроса.";
   }
}

$mysqli->close();
?>