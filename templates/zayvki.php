<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style.css">
    <?php require '../osnova/setup.php'; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои заявки</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .no_zayvka {
            color: black; 
            font-size: 30px; 
            text-align: center;
            margin-top: 400px;
        }

        main {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center; 
        }
    </style>
</head>
<body>
<main>

<?php
session_start(); 

require '../connect.php';

if (!isset($_SESSION['id_user'])) {
    echo "Пользователь не аутентифицирован.";
    exit;
}

$id_user = intval($_SESSION['id_user']); 


$sql = "SELECT * FROM zayvki WHERE id_user = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_user); 
$stmt->execute();
$result = $stmt->get_result();
$count = mysqli_num_rows($result);

if ($count > 0) {
?>

<br><table border="1" class="table">
<thead>
    <tr>
        <th>Название</th>
        <th>Описание</th>
        <th>Категория</th>
        <th>Фото</th>
    </tr>
</thead>
<tbody>

<?php
while ($row = $result->fetch_assoc()) {
?>

<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['context']); ?></td>
    <td><?php echo htmlspecialchars($row['kategoria']); ?></td>
    <td><img src="<?php echo htmlspecialchars($row['file']); ?>" alt="Фото" style="max-width: 100px; max-height: 100px;"></td>
</tr>

<?php 
} 
?>

</tbody>
</table>

<?php
} else {
    echo '<div class="no_zayvka">У вас нет заявок</div>';
}
$stmt->close(); 
$mysqli->close(); 
?>

</main>

</body>
</html>