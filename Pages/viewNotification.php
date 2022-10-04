<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
$userEmail = $_SESSION['userEmail'];
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$post = '';
$xNotification_id = '';
$isBooked = '';
if ( isset($_POST['noti_from']) && isset($_POST['notification_id']) && isset($_POST['post_id']) ){
    $notification_id = $_POST['notification_id'];
    $noti_from = $_POST['noti_from'];
    $post_id = $_POST['post_id'];
    $xNotification_id = $notification_id;

    $statement = $pdo->prepare("SELECT * FROM post where post_id = $post_id;");
    $statement->execute();
    $post= $statement->fetch(PDO::FETCH_ASSOC);

    $statement = $pdo->prepare("SELECT concat(first_name, ' ', last_name) as name, dp FROM user_info WHERE email = '$noti_from';");
    $statement->execute();
    $requestSender= $statement->fetch(PDO::FETCH_ASSOC);

    if ($post['isBooked'] == 1){
        $isBooked = "isBooked";
    }
}
else{
    header("Location: home.php");
}
$statement = $pdo->prepare("SELECT dp FROM user_info where email = '$userEmail'");
$statement->execute();
$user = $statement->fetch(PDO::FETCH_ASSOC);
$dp = $user['dp'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../CSS/viewNotification.css">
    <?php require_once "../Helper/headLinks.php" ?>
    <title>Notification | Babysitter</title>
</head>

<body>
    <section>
        <?php include_once "../Helper/header.php"?>
    </section>
    <section class="mt-3">
        <div class="mx-auto postShaddow rounded singlePost p-3" style="width: 60%; margin-bottom: 10px;">

            <img src="<?php echo $post['post_image'] ?>" class="mx-auto" alt="Post image">
            <div class="card-body mt-3">
                <h5 class="my-3 text-xl font-bold">
                    <?php echo $post['title'] ?>
                </h5>
                <p class="card-text">
                    <?php echo $post['description'] ?>
                </p>
            </div>
            <div class="flex justify-between mt-6">
                <h5 class="py-2 bg-sky-600 rounded py-1 px-2 text-white"> <i class="fa-regular fa-clock"></i>
                    <?php echo "  " . $post['hour'] ?> Hrs
                </h5>
                <h5 class="py-2 bg-sky-600 rounded py-1 px-2 text-white"> <i class="fa-solid fa-sack-dollar"></i>
                    <?php echo " " . $post['bdt'] ?> BDT/Hr
                </h5>
            </div>

        </div>
    </section>

    <section style="width: 60%; margin: 10px auto;">
        <div class="flex justify-center">
            <div><img class="reqImage border-2 border-sky-600" src="<?php echo $requestSender['dp'] ?>" alt=""></div>
            <div>
                <p class="mt-8 ml-2"><span class="text-xl text-sky-600 font-bold"><?php echo $requestSender['name'] ?></span> requests for this post</p>
            </div>
        </div>
    </section>

    <section style="width: 60%; margin: 10px auto;" class="flex justify-center my-3">
        <form action="../Helper/deleteNotification.php" class="mr-3" method="post">
            <input type="hidden" name="notificationId" value="<?php echo $xNotification_id; ?>">
            <button type="submit" class="rounded bg-red-600 hover:bg-red-700 text-white py-1 px-3">Discard</button>
        </form>
        <form action="provide.php" method="post">
            <input type="hidden" name="reqSender" value="<?php echo $noti_from; ?>">
            <input type="hidden" name="postId" value="<?php echo $post_id; ?>">
            <input type="hidden" name="notificationId" value="<?php echo $xNotification_id; ?>">
            <button type="submit" class="rounded bg-green-700 hover:bg-green-800 text-white py-1 px-3 <?php echo $isBooked; ?>">Provide</button>
        </form>
    </section>
</body>

</html>