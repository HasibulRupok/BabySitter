<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

$email = $_SESSION['userEmail'];
//    session_destroy();
//    unset($_SESSION['userEmail']);  // it will delete the session and move to login.php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $pdo->prepare("SELECT dp FROM user_info where email = '$email'");
$statement->execute();
$user = $statement->fetch(PDO::FETCH_ASSOC);

$dp = $user['dp'];

//    fetching the posts
$statement = $pdo->prepare("SELECT * FROM post ");
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);

//knock button
$id = $_POST['id'] ?? null;
if ($id) {
    //    header("Location: ../public/login.php");
}

//verified users data
$statement = $pdo->prepare("SELECT email,last_name,dp FROM `user_info` WHERE isVerified = 1;");
$statement->execute();
$verifiedUsers = $statement->fetchAll(PDO::FETCH_ASSOC);

//notifications
$statement = $pdo->prepare("SELECT * FROM notification WHERE noti_to = '$email' order by notification_id desc;");
$statement->execute();
$notifications = $statement->fetchAll(PDO::FETCH_ASSOC);

$postID=0;

//notification dynamic
$notificationIcon = "<i class='fa-solid fa-bell'></i>";
$toastNotification = 1;
if (!count($notifications)){
    $notificationIcon = "<i class='fa-solid fa-bell-slash'></i>";
    $toastNotification = 0;
}

//handling pending payments
$statement = $pdo->prepare("SELECT * FROM pending_payments WHERE authorConfirmation = 1;");
$statement->execute();
$pendingPayments = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($pendingPayments as $pendingPayment){
    $ourAmount = $pendingPayment['amount'] * 0.11;
    $receiverAmount = $pendingPayment['amount'] - $ourAmount;

    $provider = $pendingPayment['author'];
    $receiver = $pendingPayment['receiver'];
    $total = $pendingPayment['amount'];
    $pendingId = $pendingPayment['id'];

    $statement = $pdo->prepare("INSERT INTO ourBalance (amount, provider, receiver, total) VALUES($ourAmount, '$provider', '$receiver', $total);");
    $statement->execute();

    $statement = $pdo->prepare("SELECT balance FROM user_info WHERE email = '$receiver';");
    $statement->execute();
    $receiverOldBalance = $statement->fetchAll(PDO::FETCH_ASSOC);
    $receiverAmount += $receiverOldBalance['balance'];

    $statement = $pdo->prepare("UPDATE user_info SET balance = $receiverAmount WHERE email = '$receiver';");
    $statement->execute();

    $statement = $pdo->prepare("DELETE FROM pending_payments WHERE id = $pendingId;");
    $statement->execute();
}

$addresses = include_once "../Helper/addresses.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <?php require_once "../Helper/headLinks.php" ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <!--    jquery/ajax CDN-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../CSS/home.css">
    <link rel="stylesheet" href="../CSS/poopup1.css">
    <!--    font-awesome -->
    <link rel="stylesheet" href="../CSS/all.css">
</head>

<body>
    <header class="topSticky bg-sky-700">
        <!-- dark:bg-gray-800 -->
        <nav class="bg-white border-gray-200 px-2 sm:px-4 py-2.5 rounded bg-transparent">
            <div class="flex justify-between mx-6">
                <div>
                    <!-- company Logo -->
                    <a href="home.php" class="flex items-center">
                        <img src="../Helper/images/babySitter logo.PNG" class="mr-3 h-6 sm:h-9" alt="BabySitter">
                        <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">BabySitter
                            BD</span>
                    </a>
                </div>
                <div class="container flex flex-wrap justify-end items-center mx-auto">
                    <div class="flex items-center md:order-2">
                        <!-- profile image button  -->
                        <button type="button" onclick="toggleFunction()" class="flex mr-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 ml-6 popup" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                            <span class="sr-only">Open user menu</span>
                            <img class="w-8 h-8 rounded-full" src="<?php echo $dp ?>" alt="user photo">
                        </button>

                    </div>
                    <div class="hidden justify-between items-center w-full md:flex md:w-auto md:order-1" id="mobile-menu-2">
                        <ul class="flex flex-col mt-4 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium">
                            <li>
                                <a href="#" class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover" aria-current="page">Home</a>
                            </li>
                            <li>
                                <a href="makePost.php" class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover">Post</a>
                            </li>
                            <li>
                                <a href="messenger.php" class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover">Messenger</a>
                            </li>
                            <li>
                                <a href="pendingConfirmation.php" class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover">Pending Services</a>
                            </li>
                            <li>
                                <button id="notificationBtn" onclick="notificationClicked()"  class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover"><?php echo $notificationIcon; ?></button>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <hr>

    </header>

    <section class="flex">
        <div class="width10 px-2 py-5 float-right verifiedUserContainer">
            <h4 class="text-xl text-white mb-3">Verified Users</h4>
            <?php foreach ($verifiedUsers as $verifiedUser):  ?>
            <div class="flex verifiedUser">
                <form action="viewUser.php" method="post">
                    <input type="hidden" name="viewAbleUser" value="<?php echo $verifiedUser['email'] ?>">

                    <button class="flex">
                        <img src="<?php echo $verifiedUser['dp'] ?>" alt="" class="activeUserImage">
                        <p class="activeUserName"><?php echo $verifiedUser['last_name'] ?></p>
                    </button>

                </form>

            </div>
            <?php endforeach; ?>
        </div>

        <!-- post container -->
        <div class="width90 px-4 py-5 postContainer float-right">

            <?php foreach ($posts as $post) : ?>
                <?php if ($post['isBooked'] == 1) continue;  ?>
                <div class="mx-auto postShaddow rounded singlePost" style="width: 60%; margin-bottom: 10px;">

                    <img src="<?php echo $post['post_image'] ?>" class="mx-auto" alt="Post image">
                    <div class="card-body mt-3">
                        <h5 class="my-3 text-xl font-bold" id="post-title"><?php echo $post['title'] ?></h5>
                        <p class="card-text"> <?php echo $post['description'] ?> </p>
                    </div>

                    <div class="mt-3">
                        <p><i class="fa-solid fa-location-dot"></i> <span class="font-semibold"><?php echo $addresses[$post['address']]; ?></span> </p>
                    </div>

                    <div class="flex justify-between mt-6">
                        <h5 class="py-2 boxShadow"> <i class="fa-regular fa-clock"></i> <?php echo "  " . $post['hour'] ?> Hrs</h5>
                        <h5 class="py-2 boxShadow"> <i class="fa-solid fa-sack-dollar"></i> <?php echo " " . $post['bdt'] ?> BDT/Hr</h5>
                        <button class="bg-gray-400 rounded px-5 py-2 text-white knockBtn" type="submit" id="knockBtn" onclick="knockPressed()">Knock</button>
                    </div>
                    <div id="hiddeMessageDiv" class="hiddeMessageDivClass flex justify-start">
<!--                        //id="knockSms"   sendKnockBtn     -->
                        <input type="hidden" name="id" id="post_id" value=" <?php echo $post['post_id'] ?>">
                        <input class="rounded-full smsInput" type="text" id="<?=$postID.'-knockSms'?>" placeholder="write your sms here">
                        <button class="rounded-full msgSendButton" id="<?=$postID.'-sendKnockBtn'?>">Send</button>

                        <input type="hidden" name="idd" id="<?=$postID.'-postId'?>" value="<?php echo $post['post_id'] ?>">
                        <input type="hidden" name="idd" id="<?=$postID.'-email'?>" value="<?=$post['email']?>">
                        <button class="rounded-full msgSendButton" id="<?=$postID++.'-request';?>">Request</button>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>
    </section>
    <!--    toggle div (profile and log out) -->
    <div class="toggleContainer bg-sky-700" id="toggleDiv">
        <button class="tgButton" onclick="profileClick()"> <i class="fa-solid fa-user"></i> Profile </button>
        <button class="tgButton" id="signOutBtn" onclick="signOut()"> <i class="fa-solid fa-right-from-bracket"></i> Sign out</button>
    </div>

<!--    notification div-->
    <div class="notificationCon" id="notificationContainer">
        <h2 class="mb-3">Notifications:</h2>

            <?php foreach ($notifications as $notification):  ?>
                <?php
                $xNotiId = $notification['post_id'];
                $xnotiFrom = $notification['noti_from'];

                $statement = $pdo->prepare("SELECT title FROM post WHERE post_id = $xNotiId;");
                $statement->execute();
                $xPostTitle = $statement->fetch(PDO::FETCH_ASSOC);

                $statement = $pdo->prepare("SELECT concat(first_name, ' ', last_name) as xName, dp FROM user_info WHERE email = '$xnotiFrom';");
                $statement->execute();
                $xUser = $statement->fetch(PDO::FETCH_ASSOC);
                $xDp = $xUser['dp'];

                ?>
                <form class="mb-2 xNotification" method="post" action="viewNotification.php">
                    <input type="hidden" name="noti_from" value="<?php echo $notification['noti_from']; ?>">
                    <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                    <input type="hidden" name="post_id" value="<?php echo $notification['post_id']; ?>">
                    <button class="flex justify-start">
                        <div id="ntfUserImgDiv">
                            <img class="ntfUserImg mt-2" src="<?=$xDp?>">
                        </div>

                        <div class="text-left">
                            <p> <span class="font-bold"><?php echo $xUser['xName']; ?></span> request for the post</p>
                            <p><?php echo $xPostTitle['title']; ?></p>
                        </div>
                    </button>
                </form>
            <?php endforeach; ?>



    </div>

<!--    toast notification-->
    <section class="hidden flex-col justify-center" id="toastNotificationContainer" style="position: fixed; right: 0.25rem; bottom: 0;">

        <div class="bg-sky-700 shadow-lg mx-auto w-96 max-w-full text-sm pointer-events-auto bg-clip-padding rounded-lg block mb-3"
             id="static-example" role="alert" aria-live="assertive" aria-atomic="true" data-mdb-autohide="false">
            <div
                    class="bg-sky-700 flex justify-between items-center py-2 px-3 bg-clip-padding border-b border-blue-500 rounded-t-lg">
                <p class="font-bold text-white flex items-center">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="info-circle"
                         class="w-4 h-4 mr-2 fill-current" role="img" xmlns="http://www.w3.org/2000/svg"
                         viewBox="0 0 512 512">
                        <path fill="currentColor"
                              d="M256 8C119.043 8 8 119.083 8 256c0 136.997 111.043 248 248 248s248-111.003 248-248C504 119.083 392.957 8 256 8zm0 110c23.196 0 42 18.804 42 42s-18.804 42-42 42-42-18.804-42-42 18.804-42 42-42zm56 254c0 6.627-5.373 12-12 12h-88c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h12v-64h-12c-6.627 0-12-5.373-12-12v-24c0-6.627 5.373-12 12-12h64c6.627 0 12 5.373 12 12v100h12c6.627 0 12 5.373 12 12v24z">
                        </path>
                    </svg>
                    BabySitter BD
                </p>
                <div class="flex items-center">
                    <p class="text-white opacity-90 text-xs">Just now</p>
                    <button type="button"
                            class="btn-close btn-close-white box-content w-4 h-4 ml-2 text-white border-none rounded-none  focus:shadow-none focus:outline-none focus:opacity-100 hover:text-white hover:opacity-75 hover:no-underline"
                            data-mdb-dismiss="toast" aria-label="Close" id="toastNotificationBtn" onclick="toastBtn()"> X </button>
                </div>
            </div>
            <div class="p-3 bg-sky-700 rounded-b-lg break-words text-white">
                Hello, you have some notifications to check
            </div>
        </div>


    </section>

    <script src="../JS/home.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        function hideKnock() {
            let divs = document.getElementsByClassName('hiddeMessageDivClass');
            // console.log(div);
            for (x of divs) {
                x.classList.toggle('toggle2');
            }
        }
        function notificationClicked(){
            const notificationContainer = document.getElementById("notificationContainer");
            notificationContainer.classList.toggle('notiToggle');
        }

        // toast notification
        function sleep(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
        async function toastTimer() {
            const toastNotification = document.getElementById('toastNotificationContainer');
            const phpNotification = "<?php echo $toastNotification; ?>"
            if (phpNotification === '1'){
                await sleep(5000);
                toastNotification.style.display = "flex";
            }
        }
        toastTimer();
        const toastBtn = () =>{
            const toastNotification = document.getElementById('toastNotificationContainer');
            toastNotification.style.display = "none";
        }

        $(document).ready(function () {

            // $("#sendKnockBtn").on('click', function () {
            //    knockSms = $("#knockSms").val();
            //    if (!knockSms) {
            //        return;
            //    }
            //    post_id = $("#post_id").val();
            //    $.post("../Ajax/sendKnock.php",
            //         {
            //             key: knockSms,
            //             post_id: post_id
            //         },
            //         function (data, status) {
            //             if (status){
            //                 $("#knockSms").val('');
            //                 hideKnock();
            //             }
            //         }
            //    );
            // });

            $('button').on('click',function () {
                let id = $(this).attr('id');
                // console.log(id);
                if (!id.includes('request') && !id.includes('sendKnockBtn') ) {
                    return;
                }

                const arr = id.split('-');
                id = arr[0];

                // checking is it knock send or not
                if (arr[1] === "sendKnockBtn"){
                    let sms = document.getElementById(id+'-knockSms').value;
                    if (sms === "") return;

                    knockSend(id ,sms);
                    return;
                }

                let postID = document.getElementById(id+'-postId').value;
                let postOwner = document.getElementById(id+'-email').value;
                let requestSender = '<?php echo $email;?>'
                // console.log(postOwner);

                $.post("../Ajax/request.php",
                    {
                        post_id: postID,
                        postOwner: postOwner,
                        requestSender: requestSender
                    },
                    function (data, status) {
                        if (status){
                            // console.log(data);
                            hideKnock();
                        }
                    }
                );
            });

            function knockSend(id, sms){
                let postID = document.getElementById(id+'-postId').value;
                let knockSms = sms;

                $.post("../Ajax/sendKnock.php",
                    {
                        key: knockSms,
                        post_id: postID
                    },
                    function (data, status) {
                        if (status){
                            $("#knockSms").val('');
                            hideKnock();
                        }
                    }
                );
            }
        });
    </script>
</body>

</html>