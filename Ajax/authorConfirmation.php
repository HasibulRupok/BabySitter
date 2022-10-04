<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

if (isset($_POST['id'])){
    $id = $_POST['id'];
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("UPDATE pending_payments SET authorConfirmation = 1 WHERE id = $id;");
    $statement->execute();
}
