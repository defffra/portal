<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php'; 

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="style.css">

        <div class="block_yellow"></div>
        <div class="header_kab">
        <h1>Сделаем вместе<br> лучше!</h1>
        <div class="nav_kab">
                <li><a href="glavnaya/main1.php" onclick="$('main').load('glavnaya/main1.php');">Главная</a></li>
                <li><a href="index1.php">
                        <?php echo ($_SESSION['role'] === 'admin') ? 'Все заявки' : 'Мои заявки'; ?>
                </a></li>
                <li><a onclick="$('main').load('templates/create_zayvka.php');">
                <?php echo ($_SESSION['role'] === 'admin') ? 'Категории заявок' : 'Создать заявку'; ?>
                </a></li>
        </div>
        </div>
       