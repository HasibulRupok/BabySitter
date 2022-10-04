<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
if (isset($_POST['title']) && isset($_POST['phone']) && isset($_POST['desc'])){
    $title = $_POST['title'];
    $phone = $_POST['phone'];
    $desc = $_POST['desc'];
    $author = $_SESSION['userEmail'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("INSERT INTO complaintBox (author, title, phone, description) VALUES ('$author', '$title', '$phone','$desc')");
    $statement->execute();
}


