<?php

$db_server = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "portal";

$mysqli = new mysqli($db_server, $db_user, $db_password, $db_name);

if (mysqli_connect_errno()) {
    printf("Соединение произошло с ошибкой: %s\n", $mysqli_connect_errno());
    exit();
}


$mysqli->set_charset("utf8");
?>