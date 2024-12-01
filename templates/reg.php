<?php
session_start();
$_SESSION['fio'] = $_POST['fio'];
$_SESSION['email'] = $_POST['email'];
$_SESSION['login'] = $_POST['login'];
$conn = new mysqli('localhost', 'root', '', 'portal');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fio = $_POST['fio'];
    $email = $_POST['email'];
    $login = $_POST['login'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $consent = isset($_POST['consent']) ? 1 : 0;

    // Проверка на совпадение паролей
    if ($password !== $confirm_password) {
        die("Пароли не совпадают.");
    }

    // Хеширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Подготовленный запрос для вставки данных
    $stmt = $conn->prepare("INSERT INTO users (fio, email, login, password, consent, role) VALUES (?, ?, ?, ?, ?, 'user')");
    $stmt->bind_param("ssssi", $fio, $email, $login, $hashed_password, $consent);
    $_SESSION['id_user'] = $conn->insert_id; 
    $_SESSION['fio'] = $fio;
    $_SESSION['email'] = $email;
    $_SESSION['login'] = $login;
    if ($stmt->execute()) {
        $_SESSION['id'] = $stmt->id_user;
        header("Location: ../glavnaya/main1.php"); 
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>