<?php
session_start();
require '../osnova/setup.php'; // Подключение к базе данных

// Проверка на авторизацию
if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}

$id_user = $_SESSION['id_user']; // Предполагается, что id_user хранится в сессии

// Получение категорий из базы данных
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php'; // Подключение к базе данных

$sql_categories = "SELECT * FROM categories"; // Запрос для получения категорий
$result_categories = $mysqli->query($sql_categories);

// Проверка на наличие ошибок при выполнении запроса
if (!$result_categories) {
    die("Ошибка запроса: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать заявку</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
    <?php require '../osnova/header1.php'; ?>
</header>
<main>
    <div class="create">
        <p>Создать заявку</p>
    </div>
    <form id="sozdanie" class="sozdanie" method="POST" action='insert.php' enctype="multipart/form-data">
        <p>Создать заявку</p>
        <div class="block_zayvka">
            <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>"> 
            <label for="name">Название:</label>
            <input type="text" name="name" class="name" required> <br>

            <label for="context">Описание:</label>
            <textarea name="context" required></textarea><br>

            <label for="kategoria">Категория:</label>
            <select name="kategoria" id="kategoria" required>
                <option value="">Выберите категорию</option>
                <?php
                while ($category_name = $result_categories->fetch_assoc()) {
                    echo '<option value="' . htmlspecialchars($category_name['id']) . '">' . htmlspecialchars($category_name['category_name']) . '</option>';
                }
                ?>
            </select><br>

            <label for="file">Фото:</label>
            <input type="file" name="file" class="filename" accept="image/*" required><br>

            <button type="submit" class="btn_sozd">Создать</button>
        </div>
    </form>

    <!-- Модальное окно -->
    <div id="modal_zayvka" class="modal_zayvka" style="display:none;">
        <div class="modal_zayvka_content">
            <span class="close">&times;</span>
            <p id="modalMessage"></p>
        </div>
    </div>

    <script>
// JavaScript код для обработки отправки формы
document.getElementById('sozdanie').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const formData = new FormData(this);

    fetch('insert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        showModal(data.message);
    })
    .catch(error => {
        console.error('Ошибка:', error);
        showModal('Произошла ошибка при отправке данных.');
    });
});

function showModal(message) {
    document.getElementById('modalMessage').innerText = message;
    document.getElementById('modal_zayvka').style.display = 'block';
}

// Закрытие модального окна
document.querySelector('.close').onclick = function() {
    document.getElementById('modal_zayvka').style.display = 'none';
}
</script>

</main>

</body>
</html>

<?php
$result_categories->close(); // Закрываем результат выборки категорий
$mysqli->close(); // Закрываем соединение с базой данных
?>