<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

if (isset($_POST['notificationId'])){
    $notificationId = $_POST['notificationId'];

    $pdp = require_once "databaseConnector.php";
    $statement = $pdp->prepare("DELETE FROM notification WHERE notification_id = '$notificationId';");
    $statement->execute();
}
header("Location: ../Pages/home.php");
