<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'portal');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, fio, email, password, role FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $stmt->bind_result($id_user, $fio, $email, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['id_user'] = $id_user; 
        $_SESSION['login'] = $login;
        $_SESSION['fio'] = $fio;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role; // Сохраняем роль пользователя в сессии

        // Перенаправление на соответствующую страницу
        if ($role === 'admin') {
            header("Location: admin.php"); // Страница для администраторов
        } else {
            header("Location: ../glavnaya/main1.php"); // Страница для пользователей
        }
        exit();
    } else {
        echo "Неверный логин или пароль.";
    }
    $stmt->close();
}

$conn->close();
?>