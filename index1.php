<?php
session_start();
require $_SERVER['DOCUMENT_ROOT'] . '/portal/connect.php'; 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
     <? require 'osnova/setup.php'; ?>
</head>

<body>
     <header>

          <? require 'osnova/header1.php'; ?>
     </header>

     <main class="container">
          <?php 
     if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
          require 'templates/admin.php'; 
      } else {
          require 'templates/user.php'; 
      }
     ?>
     </main>
</body>

</html>