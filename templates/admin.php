<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="../style.css">
    <?php require '../osnova/setup.php'; ?>
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
        .edit-button {
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
<header>
    <div class="block_yellow"></div>
    <div class="header_kab">
        <h1>Сделаем вместе<br> лучше!</h1>
        <div class="nav_kab">
            <li><a href='../glavnaya/main1.php'>Главная</a></li>
            <li><a href="#zayavka_kab">Все заявки</a></li>
            <li><a href="#kategorii">Категории заявок</a></li>
        </div>
    </div>
</header>

<main>
<div class="lichkab">
<p>Личный кабинет</p>
</div>

<div class="info_user">
    <img src="../img/user.png" alt="photouser">
    <div class="info_user_dann">
        <p>ФИО: <?php echo htmlspecialchars($_SESSION['fio']); ?></p>
        <p>Логин: <?php echo htmlspecialchars($_SESSION['login']); ?></p>
        <p>E-mail: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    </div>
</div>

<button class="exitkab"><a href="../templates/logout.php">Выйти из аккаунта</a></button>

<div class="zayavka_kab" id='zayavka_kab'>
<h2>ВСЕ ЗАЯВКИ</h2>

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

$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Изменяем SQL-запрос для получения заявок с JOIN для получения названий категорий
$sql = "
    SELECT z.*, c.category_name 
    FROM zayvki z 
    LEFT JOIN categories c ON z.category_id = c.id 
";
if ($status_filter) {
    $sql .= " WHERE z.status = ?";
}
$sql .= " ORDER BY z.date DESC";

$stmt = $mysqli->prepare($sql);
if ($status_filter) {
    $stmt->bind_param("s", $status_filter); // Привязываем параметры
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
        
        echo '<p>Категория - ' . htmlspecialchars($row['category_name']) . '</p>'; 
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . '/portal/uploads/' . htmlspecialchars($row['file']);
        if (file_exists($target_dir)) {
            echo '<img src="/portal/uploads/' . htmlspecialchars($row['file']) . '" alt="Загруженное фото" style="max-width: 10%; height: auto; margin-left:60px; margin-top:20px">';
        } else {
            echo '<p>Фото отсутствует.</p>';
        }
        if ($row['status'] == 'rejected') {
            echo '<p>Причина отказа - ' . htmlspecialchars($row['reason_rejection']) . '</p>'; 
        }

        // Отображение статуса заявки
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

        // Форма для смены статуса только для новых заявок
        if ($row['status'] == 'new') { 
            echo '<form class="change_status" action="change_status.php" method="POST" enctype="multipart/form-data">';
            echo '<input type="hidden" name="request_id" value="' . htmlspecialchars($row['id']) . '">'; 
            echo '<select name="new_status" required onchange="toggleFields(this.value, ' . htmlspecialchars($row['id']) . ')">';
            echo '<option value="">Выберите статус</option>';
            echo '<option value="resolved">Решена</option>';
            echo '<option value="rejected">Отклонена</option>';
            echo '</select>';
        
            // Поле для загрузки фотографии
            echo '<div id="photo-container-' . htmlspecialchars($row['id']) . '" style="display:none;">';
            echo '<label for="photo-' . htmlspecialchars($row['id']) . '">Прикрепите фотографию:</label>';
            echo '<input type="file" name="photo" id="photo-' . htmlspecialchars($row['id']) . '" accept="image/*">';
                    
            echo '<button type="submit">Сменить статус</button>';
            echo '</div>';
        
            // Поле для указания причины отказа
            echo '<div id="reason-container-' . htmlspecialchars($row['id']) . '" style="display:none;">';
            echo '<label for="reason-' . htmlspecialchars($row['id']) . '">Причина отказа:</label>';
            echo '<input type="text" name="reason" id="reason-' . htmlspecialchars($row['id']) . '">';
    
            echo '<button type="submit">Сменить статус</button>';
            echo '</div>';
            echo '</form>';
        }
        
        echo '</div>'; // Закрывающий тег для zayavka_1
    }
    echo '</div>'; // Закрывающий тег для zayavka_list
} else {
    echo '<div class="no_zayvka">Заявок нет.</div>';
}
$stmt->close(); 
$mysqli->close(); 
?>
</div> <!-- Закрывающий тег для requests-container -->
</div> <!-- Закрывающий тег для zayavka_kab -->
<div class="kategorii" id='kategorii'>
<h2>КАТЕГОРИИ ЗАЯВОК</h2>
<div class="block_kategorii">
<form action="../templates/add_category.php" method="POST" class="add-category">
<input type="text" name="category_name" placeholder="Название категории" required>
<button type="submit" class="dobav_kat">Добавить</button>
</form>
<div class="spisok_kat">
<p>НАИМЕНОВАНИЕ</p>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php';  
$sql_categories = "SELECT * FROM categories"; 
$result_categories = $mysqli->query($sql_categories);
if ($result_categories && mysqli_num_rows($result_categories) > 0) {
    while ($category = $result_categories->fetch_assoc()) {
        echo '<li>' . htmlspecialchars($category['category_name']) . '
            <form action="../templates/delete_category.php" method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                <input type="hidden" name="category_id" value="' . htmlspecialchars($category['id']) . '">
                <button type="submit" class="delete-category">&#128465;</button>
            </form></li>';
    }
} else {
    echo '<li>Нет категорий.</li>';
}
$result_categories->close();
?>
</div><!-- Закрывающий тег для spisok_kat --></div><!-- Закрывающий тег для block_kategorii --></div><!-- Закрывающий тег для kategorii -->
</main>
<script>
// Подтверждение удаления заявки
function confirmDelete() {
    return confirm('Вы уверены, что хотите удалить эту заявку?');
}

// Сортировка заявок
function sortRequests() {
    var status = document.getElementById('sort').value;
    if (status === '') {
    window.location.href = 'admin.php'; // Замените на ваш скрипт для получения всех заявок
    } else {
        window.location.href = 'admin.php?status=' + status; // Перенаправляем с выбранным статусом
    }
}

function toggleFields(status, requestId) {
    const photoContainer = document.getElementById('photo-container-' + requestId);
    const reasonContainer = document.getElementById('reason-container-' + requestId);
    
    if (status === 'resolved') {
        photoContainer.style.display = 'block'; // Показываем контейнер для фото
        reasonContainer.style.display = 'none';  // Скрываем контейнер для причины отказа
    } else if (status === 'rejected') {
        photoContainer.style.display = 'none';   // Скрываем контейнер для фото
        reasonContainer.style.display = 'block'; // Показываем контейнер для причины отказа
    } else {
        photoContainer.style.display = 'none';   // Скрываем контейнер для фото
        reasonContainer.style.display = 'none';   // Скрываем контейнер для причины отказа
    }
}
</script>
</body>
</html>