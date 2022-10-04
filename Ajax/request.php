<?php
if (isset($_POST['post_id']) && isset($_POST['postOwner']) && isset($_POST['requestSender'])){
    $post_id = $_POST['post_id'];
    $postOwner = $_POST['postOwner'];
    $requestSender = $_POST['requestSender'];
    $pdo = require_once "../Helper/databaseConnector.php";

    $statement = $statement = $pdo->prepare("INSERT INTO notification (noti_from, noti_to, post_id) VALUES('$requestSender', '$postOwner', $post_id);");
    $statement->execute();

}