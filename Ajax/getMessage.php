<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

if (isset($_POST['user']) && isset($_POST['person'])){
    $user_email = $_POST['user'];
    $personEmail = $_POST['person'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("SELECT message as text,from_id, isPost,postId FROM chats WHERE from_id IN('$user_email', '$personEmail') and to_id IN('$user_email', '$personEmail') ORDER BY chat_id ASC ;");
    $statement->execute();
    $messages = $statement->fetchALL(PDO::FETCH_ASSOC);

    ?>

    <?php foreach ($messages as $message):  ?>
        <?php if ($message['from_id'] == $user_email): ?>
            <p class="right chat"><?php echo $message['text']; ?></p> <br>
        <?php endif; ?>
        <?php if ($message['from_id'] != $user_email): ?>
            <?php if ($message['isPost'] == 1): ?>
                <?php
                $postId = $message['postId'];
                $statement = $pdo->prepare("SELECT title from post where post_id='$postId';");
                $statement->execute();
                $postInfo = $statement->fetch(PDO::FETCH_ASSOC);
                ?>
                <p class="left chat"><span class="yellow_bg"><?php echo $postInfo['title'].'<br>'; ?></span> <?php echo $message['text']; ?></p> <br>
                <?php continue; ?>
            <?php endif; ?>
            <p class="left chat"><?php echo $message['text']; ?></p> <br>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php

}