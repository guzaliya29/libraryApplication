<?php
	$link = mysqli_connect("localhost", "root", "", "db_library");
if (!$link) {
    die('Ошибка подключения: ' . mysqli_connect_error());
}
mysqli_set_charset($link, 'utf8mb4');
?>