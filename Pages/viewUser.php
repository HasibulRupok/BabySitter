<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

if (!isset($_POST['viewAbleUser'])){
    header("Location:home.php");
}
$viewAbleUser = $_POST['viewAbleUser'];
$email = $_SESSION['userEmail'];

$pdo = require_once "../Helper/databaseConnector.php";

$statement = $pdo->prepare("SELECT dp FROM user_info where email = '$email';");
$statement->execute();
$owner = $statement->fetch(PDO::FETCH_ASSOC);
$dp = $owner['dp'];

$statement = $pdo->prepare("SELECT * FROM user_info where email = '$viewAbleUser'");
$statement->execute();
$user = $statement->fetch(PDO::FETCH_ASSOC);

$statement = $pdo->prepare("SELECT * FROM verifiedUser where email = '$viewAbleUser'");
$statement->execute();
$verifiedUser = $statement->fetch(PDO::FETCH_ASSOC);


$verifiedSign = '';
$displayNone = '';
if ($user['isVerified'] != 0){
    $verifiedSign = "<i class='fa-regular fa-circle-check blueColor'></i>";
    $displayNone = "displayNone";
}

//calculating ratings and stars
$liveRating = 0;
$star = "";
$happyClients = 0;
if ($user['total_clients']){
    $happyClients = $user['total_clients'] - 1;
    $liveRating = $user['total_rating'] / $user['total_clients'];

    if ($liveRating < 1){
        $star = "<i class='fa-solid fa-star-half-stroke'></i>";
    }
    else{
        for ($i=1; $i<=$liveRating; $i++){
            $star.="<i class='fa-solid fa-star'></i>";
        }
    }
    if (is_float($liveRating) && $liveRating >= 1){
        $star.= "<i class='fa-solid fa-star-half-stroke'></i>";
    }
}
//echo "<pre>";
//echo " <br>";
//echo " <br>";
//echo " <br>";
//echo " <br>";
//var_dump($user);
//echo "</pre>";


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once "../Helper/headLinks.php" ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!--    jquery/ajax CDN-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="../CSS/header.css">
    <link rel="stylesheet" href="../CSS/viewUser.css">

    <title>Profile</title>
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
                    <button type="button" onclick="toggleFunction()"
                            class="flex mr-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 ml-6 popup"
                            id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="<?php echo $dp?>" alt="user photo">
                    </button>

                </div>
                <div class="hidden justify-between items-center w-full md:flex md:w-auto md:order-1"
                     id="mobile-menu-2">
                    <ul class="flex flex-col mt-4 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium">
                        <li>
                            <a href="home.php"
                               class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover"
                               aria-current="page">Home</a>
                        </li>
                        <li>
                            <a href="makePost.php"
                               class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover disable-links">Post</a>
                        </li>
                        <li>
                            <a href="#"
                               class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover">Update
                                info</a>
                        </li>
                        <li>
                            <a href="#"
                               class="block py-2 pr-4 pl-3 ttext-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover">About</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <hr>

</header>

<section>
    <div class="profileCover">
        <img class="coverPic" src="../Helper/images/cover.jpg" alt="">
    </div>
    <div class="profileContainer">
        <div class="profilePic">
            <img class="profileImage" src="<?php echo $user['dp'];?>" alt="DP">
        </div>

        <h1 class="userName text-center text-3xl"><?php echo $user['first_name']." ".$user['last_name']." ".$verifiedSign ?></h1>
        <div class="rating">
            <?php echo $star ?>
        </div>

        <div class="mb-3">
            <?php $addresses = include_once "../Helper/addresses.php"; ?>
            <p class="text-center"><i class="text-sky-600 fa-solid fa-location-dot"></i> <span class="font-normal"><?php echo $addresses[$user['address']]; ?></span> </p>
        </div>

        <div class="about text-justify w-3/5">
            <p><?php echo $verifiedUser['description']?></p>
        </div>

        <div class="workHistory w-3/5 mx-auto mt-6">
            <h2 class="text-2xl mb-4 underline" id="workHistory">Work History</h2>
            <table class="table-auto w-4/5 mb-6">
                <thead>
                <tr>
                    <th class="underline">Title</th>
                    <th class="underline">Performance</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">Total number of sit(s)</td>
                    <td class="text-center"><?php echo $user['total_clients'] ?></td>
                </tr>
                <tr>
                    <td class="text-center">Happy Clients</td>
                    <td class="text-center"><?php echo $happyClients ?></td>
                </tr>
                <tr>
                    <td class="text-center">Rating</td>
                    <td class="text-center"><?php echo $liveRating ?></td>
                </tr>
                </tbody>
            </table>
        </div>

    </div>
    <div class="w-3/5 mx-auto my-6 <?php echo $displayNone ?>">
        <form action="verifiUser.php" method="post">
            <button type="submit" class="applyButton">apply for verification</button>
        </form>
    </div>
</section>
<section>

</section>


<!--    toggle div (profile and log out) -->
<div class="toggleContainer bg-sky-700" id="toggleDiv">
    <button class="tgButton" disabled> <i class="fa-solid fa-user"></i> Profile </button>
    <button class="tgButton" id="signOutBtn" onclick="signOut()"> <i class="fa-solid fa-right-from-bracket"></i>
        Sign out</button>
</div>



<script src="../JS/toggle.js"></script>
</body>

</html>