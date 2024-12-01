<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php'; 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../style.css">
<? require '../osnova/setup.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
     <header>
          <? require '../osnova/header.php'; ?>
     </header>
<main class="container"> 
<nav>
    <?php 
    $profileUrl = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? '../templates/admin.php' : '../index1.php'; 
    ?>
    <a href="<?php echo $profileUrl; ?>" class="kabinet"><b>Личный кабинет</b></a>
</nav>

<div class="foto2"></div>
<div class="block1">

<div class="text1">
<h2>Ямочный ремонт дорог</h2>
<p>Ремонт дороги</p>
<h3>10.06.2023</h3>
</div>
</div>

<div class="foto3"></div>
<div class="block2">


<div class="text2">
<h2>Ремонт детской площадки</h2>
<p>Ремонт площадки</p>
<h3>10.06.2023</h3>
</div>


</div>
<div class="foto4"></div>
<div class="block3">
<div class="text3">
<h2>Реконструкция здания</h2>
<p>Замена фасада</p>
<h3>10.06.2023</h3>
</div>

</div>

<div class="block4">
    <h1>Количество решенных заявок:</h1>
    <div class="chet"></div>
</div>
</main>

<script>
  const min = 10;
const max = 1000;
const time = 2000000;

(function step(val) {
  setTimeout(function() {
    document.querySelector('.chet').innerText = val;
    if (val < max) {
      step(val + 1);
    }
  }, time / (max - min));
})(min);


function loadPage(page) {
    $.get(page, function(data) {
        $('main').html(data); // Загружаем содержимое в основной блок
    });
}

</script>
</body>
</html>
