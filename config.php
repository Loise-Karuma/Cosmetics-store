<?php

$dsn = "mysql:host=localhost;dbname=cosmetics;port=3306";
$username = "root";
$password = "";

$conn = new PDO($dsn, $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>