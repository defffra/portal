<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .no_zayvka {
            color: black; 
            font-size: 30px; 
            text-align: center;
            margin-top: 50px;
        }
        form {
            margin-top: -45px;
        }
        .delete-button {
            background-color: #EDDA2C;
            width: 180px;
            height: 45px;
            border-radius: 10px;
            margin-left: 370px;
            border: none;
            font-weight: 400;
            font-size: 20px;
            color: #000;
            font-family: 'Montserrat Alternates';
        }
    </style>
</head>

<body>
        <div class="block_yellow"></div>
        <div class="header_kab">
        <h1>Сделаем вместе<br> лучше!</h1>
        <div class="nav_kab">
                <li><a href="glavnaya/main1.php" onclick="$('main').load('glavnaya/main1.php');">Главная</a></li>
                <li><a href="#zayavka_kab">
                        <?php echo ($_SESSION['role'] === 'admin') ? 'Все заявки' : 'Мои заявки'; ?>
                </a></li>
                <li><a onclick="$('main').load('templates/create_zayvka.php');">
                <?php echo ($_SESSION['role'] === 'admin') ? 'Категории заявок' : 'Создать заявку'; ?>
                </a></li>
        </div>
        </div>
       
<main>
<div class="lichkab">
<p>Личный кабинет</p>
</div>
<div class="info_user">
    <img src="img/user.png" alt="photouser">
    <div class="info_user_dann">
        <p>ФИО: <?php echo htmlspecialchars($_SESSION['fio']); ?></p>
        <p>Логин: <?php echo htmlspecialchars($_SESSION['login']); ?></p>
        <p>E-mail: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    </div>
</div>
<button class="exitkab"> <a href="templates/logout.php">Выйти из аккаунта </a></button>

<div class="zayavka_kab" id="zayavka_kab">
<h2>МОИ ЗАЯВКИ</h2>

<div class="sort_kab">
<p>Сортировка:
    <select name="sort" id="sort" onchange="sortRequests()">
        <option value="">Все заявки</option>
        <option value="new">Новая</option>
        <option value="rejected">Отклонена</option>
        <option value="resolved">Решена</option>
    </select>
</p>
</div>

<div id="requests-container">
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php';  

// Проверяем, что пользователь аутентифицирован
if (!isset($_SESSION['id_user'])) {
    echo "Пользователь не аутентифицирован.";
    exit;
}

$id_user = intval($_SESSION['id_user']); // Получаем ID пользователя из сессии

// Получаем статус для фильтрации
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Изменяем SQL-запрос для получения заявок конкретного пользователя с JOIN
$sql = "
    SELECT z.*, c.category_name 
    FROM zayvki z 
    LEFT JOIN categories c ON z.category_id = c.id 
    WHERE z.id_user = ?";
if ($status_filter) {
    $sql .= " AND z.status = ?";
}
$sql .= " ORDER BY z.date DESC";

$stmt = $mysqli->prepare($sql);
if ($status_filter) {
    $stmt->bind_param("is", $id_user, $status_filter); // Привязываем параметры
} else {
    $stmt->bind_param("i", $id_user); // Привязываем только ID пользователя
}
$stmt->execute();
$result = $stmt->get_result();
$count = mysqli_num_rows($result);

if ($count > 0) {
    echo '<div class="zayavka_list">';
    
    while ($row = $result->fetch_assoc()) {
        echo '<div class="zayavka_1">';
        echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p>' . htmlspecialchars($row['context']) . '</p><br>';
        echo '<p>Категория - ' . htmlspecialchars($row['category_name']) . '</p>'; // Вывод названия категории
        
        $target_dir = 'uploads/' . htmlspecialchars($row['file']);
        
        if (file_exists($target_dir)) {
            echo '<img src="' . $target_dir . '" alt="Загруженное фото" style="max-width: 10%; height: auto; margin-left:60px; margin-top:20px">';
        } else {
            echo '<p>Фото отсутствует.</p>';
        }
        if ($row['status'] == 'rejected') {
            echo '<p>Причина отказа - ' . htmlspecialchars($row['reason_rejection']) . '</p>'; 
        }
        echo '<p style="color: #EDDA2C; font-size: 30px; font-weight: 400; margin-top: 50px">' . htmlspecialchars($row['date']) . '</p>'; 
        echo '<div class="status">';
        
        $status = '';
        if ($row['status'] == 'resolved') {
            $status = '&#x2713; РЕШЕНА'; // Заявка решена
        } elseif ($row['status'] == 'rejected') {
            $status = '&#215; ОТКЛОНЕНА'; // Заявка отклонена
        } elseif ($row['status'] == 'new') { 
            $status = '&#8853; НОВАЯ'; // Заявка новая
        }
        
        echo $status;
        echo '</div>';
        
        // Кнопка удаления только для новых заявок
        if ($row['status'] == 'new') { 
            echo '<form action="templates/delete_request.php" method="POST" style="display:inline;" onsubmit="return confirmDelete();">';
            echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($row['id']) . '">'; 
            echo '<button type="submit" class="delete-button">Удалить</button>';
            echo '</form>';
        }

        echo '</div>'; // Закрывающий тег для zayavka_1
    }
    echo '</div>'; // Закрывающий тег для zayavka_list
} else {
    echo '<div class="no_zayvka">У вас нет заявок.</div>';
}

$stmt->close(); 
$mysqli->close(); 
?>
</div> <!-- Закрывающий тег для requests-container -->
</div>
</main>

<script>
function confirmDelete() {
    return confirm('Вы уверены, что хотите удалить эту заявку?');
}

function sortRequests() {
    var status = document.getElementById('sort').value;
    window.location.href = '?status=' + status; 
}
</script>

</body>
</html>
