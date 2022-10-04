<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
$notiId = -99;
if (isset($_POST['reqSender']) && isset($_POST['notificationId']) && isset($_POST['postId']) && isset($_POST['amount']) ){
    $reqSender = $_POST['reqSender'];
    $notiId = $_POST['notificationId'];
    $postId = $_POST['postId'];
    $amount = $_POST['amount'];
    $userEmail = $_SESSION['userEmail'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("INSERT INTO pending_payments (amount, author, receiver, postId) VALUES($amount, '$userEmail', '$reqSender', $postId);");
    $statement->execute();

    $statement = $pdo->prepare("UPDATE post SET isBooked=1 WHERE post_id= $postId;");
    $statement->execute();

    $statement = $pdo->prepare("DELETE FROM notification WHERE notification_id = $notiId;");
    $statement->execute();
}
else{
    header("Location: home.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../CSS/paymentConfirmation.css">
    <!--    font-awesome -->
    <link rel="stylesheet" href="../CSS/all.css">
    <?php require_once "../Helper/headLinks.php" ?>
    <title>BabysitterBD</title>
</head>

<body>
    <?php include_once "../Helper/header2.php"?>
    <!-- payment confirmation -->
    <section class="w-2/4 mx-auto mt-24">
        <h2 class="font-medium text-xl my-1">Your payment received successfully</h2>
        <p class="my-1">You can get the request sender information now</p>
        <div>
            <button class="bg-sky-600 px-2 py-1 rounded text-white" id="getInfoBtn"> Get Info</button>
        </div>
    </section>


    <section class="hidden" id="infoContainer">
        <div class="">
            <img class="senderImg" src="../Helper/images/img_avatar.png" alt="user image">
        </div>
        <h1 class="userName">User Name here</h1>
        <address class="my-1 reqSenderInfo"> <i class="fa-solid fa-location-dot"></i> address is here</address>
        <p class="my-1 reqSenderInfo"> <i class="fa-solid fa-address-card"></i> 0011223344</p>
    </section>

    <script>
        document.getElementById("getInfoBtn").addEventListener('click', function () {
            const section = document.getElementById("infoContainer");
            section.style.display = "block";
        });
    </script>
</body>

</html>