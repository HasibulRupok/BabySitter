<?php

session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
//current user
$user_email = $_SESSION['userEmail'];
if (isset($_POST['key']) && isset($_POST['post_id'])){
    $knockSms = $_POST['key'];
    $post_id = $_POST['post_id'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("Select email from post where post_id = $post_id;");
    $statement->execute();
    $post_email = $statement->fetch(PDO::FETCH_ASSOC);
    $post_email = $post_email['email'];

    $statement = $pdo->prepare("insert into chats (from_id, to_id, message, isPost, postId) values ('$user_email', '$post_email', '$knockSms', 1, $post_id);");
    $statement->execute();

}