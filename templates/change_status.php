<?php
session_start();
require '../connect.php'; // Подключение к базе данных

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = intval($_POST['request_id']);
    $new_status = $_POST['new_status'];

    // Проверяем текущий статус заявки
    $stmt = $mysqli->prepare("SELECT status FROM zayvki WHERE id = ?");
    if ($stmt === false) {
        die('Ошибка подготовки запроса: ' . htmlspecialchars($mysqli->error));
    }

    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_status = $row['status'];

        if ($current_status === 'new') {
            if ($new_status === 'resolved' && isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
                // Обработка загрузки фотографии
                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/portal/uploads/';
                $uploadFilePath = $uploadDir . basename($_FILES['photo']['name']);

                // Перемещение загруженного файла
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFilePath)) {
                    // Обновление статуса заявки в базе данных
                    $stmt_update = $mysqli->prepare("UPDATE zayvki SET status = ?, file = ? WHERE id = ?");
                    if ($stmt_update === false) {
                        die('Ошибка подготовки запроса: ' . htmlspecialchars($mysqli->error));
                    }
                    $stmt_update->bind_param("ssi", $new_status, basename($_FILES['photo']['name']), $request_id);
                    if ($stmt_update->execute()) {
                        echo "Статус успешно изменен на 'Решена'.";
                    } else {
                        echo "Ошибка при обновлении статуса.";
                    }
                    $stmt_update->close();
                } else {
                    echo "Ошибка при загрузке фотографии.";
                }
            } elseif ($new_status === 'rejected') {
                // Получаем причину отказа
                if (!empty($_POST['reason'])) {
                    $reason = $_POST['reason'];
                    
                    // Обновление статуса заявки в базе данных с указанием причины отказа
                    $stmt_update = $mysqli->prepare("UPDATE zayvki SET status = ?, reason_rejection = ? WHERE id = ?");
                    if ($stmt_update === false) {
                        die('Ошибка подготовки запроса: ' . htmlspecialchars($mysqli->error));
                    }
                    $stmt_update->bind_param("ssi", $new_status, $reason, $request_id);
                    if ($stmt_update->execute()) {
                        echo "Статус успешно изменен на 'Отклонена'.";
                    } else {
                        echo "Ошибка при обновлении статуса.";
                    }
                    $stmt_update->close();
                } else {
                    echo "Необходимо указать причину отказа.";
                }
            }
        } else {
            echo "Смена статуса невозможна. Статус заявки не 'Новая'.";
        }
    } else {
        echo "Заявка не найдена.";
    }
    if ($stmt->execute()) {
        header("Location: admin.php"); 
    } else {
        echo "Ошибка: " . $stmt->error;
    }
    $stmt->close(); 
}
$mysqli->close(); 
?>