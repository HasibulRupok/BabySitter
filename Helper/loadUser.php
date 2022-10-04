<?php
$pdo = require_once "databaseConnector.php";

$email = $_SESSION['userEmail'];
$statement = $pdo->prepare("SELECT * FROM user_info where email = '$email'");
$statement->execute();
$user = $statement->fetch(PDO::FETCH_ASSOC);

return $user;