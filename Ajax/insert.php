<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

if (isset($_POST['message']) && isset($_POST['to_id'])){
    $text = $_POST['message'];
    $to_id = $_POST['to_id'];
    $from_id = $_SESSION['userEmail'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statement = $pdo->prepare("INSERT INTO chats (from_id, to_id, message) VALUES('$from_id', '$to_id', '$text');");
    $statement->execute();

    ?>
    <p class="right chat"><?php echo $text; ?></p> <br>
    <?php
}