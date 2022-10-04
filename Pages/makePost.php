<?php

session_start();
if (!isset($_SESSION['userEmail'])){
    header("Location: ../public/login.php");
    exit();
}
$email = $_SESSION['userEmail'];

$post_title = '';
$post_description = '';
$post_time = '';
$post_bdt = '';
$post_image = '';
$image_path = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $post_title = $_POST['title'];
    $post_description = $_POST['description'];
    $post_time = $_POST['time'];
    $post_bdt = $_POST['bdt'];

    $image = $_FILES['image'] ?? null;

    if (!$post_title || !$post_bdt || !$post_description || !$post_time || !$image || !$image['tmp_name']){
        $error_message = "Everything is required";
    }
    else{
        if ($image && $image['tmp_name']){
            $image_path = 'Post_images/' . randomDirectory(8) . '/' . $image['name'];
            mkdir(dirname($image_path));

            move_uploaded_file($image["tmp_name"], $image_path);
        }

//    upload post info to the database
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



        $statement = $pdo->prepare("SELECT address FROM user_info where email = '$email'");
        $statement->execute();
        $address = $statement->fetch(PDO::FETCH_ASSOC);
        $addIndex = (int) $address;
        $addressX = include_once "../Helper/addresses.php";
        $addressFinal = $addressX[$addIndex];



        $statement = $pdo->prepare("INSERT INTO post (email, title, description, 	hour, bdt, post_image, address) VALUES (:email, :title, :description, :hour, :bdt, :post_image, :address)");
        $statement->bindValue(':email', $email);
        $statement->bindValue('title', $post_title );
        $statement->bindValue('description', $post_description);
        $statement->bindValue(':hour', $post_time);
        $statement->bindValue(':bdt', $post_bdt);
        $statement->bindValue(':post_image', $image_path );
        $statement->bindValue(':address', $addIndex );



//        $statement = $pdo->prepare("INSERT INTO post (email, title, description, 	hour, bdt, post_image, address) VALUES ('$email', '$post_title', '$post_description', $post_time, $post_bdt, '$image_path', '$addressFinal')");

        $statement->execute();

        header("Location: home.php");
    }


}


function randomDirectory($n){
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $str = "";
    for ($i=0; $i<$n; $i++){
        $index = rand(0, strlen($chars)-1);
        $str .= $chars[$index];
    }
    return $str;
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Make Post</title>
     <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/makePost.css">
</head>

<body>

<header class="topSticky bg-sky-700">
    <?php

    $userEmail = $_SESSION['userEmail'];

    $pdo2 = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo2->prepare("SELECT dp FROM user_info where email = '$userEmail'");
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_ASSOC);
    $dp = $user['dp'];
    ?>
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
                <div class="flex items-center md:order-2 mt-3">
                    <!-- profile image button  -->
                    <button type="button" onclick="toggleFunction()" class="flex mr-3 text-sm bg-gray-800 rounded-full md:mr-0 focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600 ml-6 popup" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="dropdown">
                        <span class="sr-only">Open user menu</span>
                        <img class="w-8 h-8 rounded-full" src="<?php echo $dp ?>" alt="user photo">
                    </button>

                </div>
                <div class="hidden justify-between items-center w-full md:flex md:w-auto md:order-1" id="mobile-menu-2">
                    <ul class="flex flex-col mt-4 md:flex-row md:space-x-8 md:mt-0 md:text-sm md:font-medium">
                        <li>
                            <a href="home.php" class="block py-2 pr-4 pl-3 text-white rounded md:bg-transparent md:text-white md:p-0 dark:text-white buttonHover" aria-current="page">Home</a>
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

                    </ul>
                </div>
            </div>
        </div>
    </nav>
    <hr>

</header>

    <h2 class="text-center heddingText font-medium">Make a post on BabySitter BD</h2>
    <section class="postFormSec">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Post title</label>
                <input type="text" class="form-control" name="title" value="<?php echo $post_title ?>" id="exampleFormControlInput1" placeholder="Your post title">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">Post description</label>
                <textarea class="form-control" name="description" value="<?php echo $post_description ?>" id="exampleFormControlTextarea1" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label for="formFile" class="form-label">Select image</label>
                <input class="form-control" type="file" name="image" id="formFile">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">Approximate time</span>
                <input type="number" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default" name="time" value="<?php echo $post_time ?>">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text" id="inputGroup-sizing-default">BDT per hour</span>
                <input type="number" class="form-control" aria-label="Sizing example input"
                    aria-describedby="inputGroup-sizing-default" name="bdt" value="<?php echo $post_bdt ?>">
            </div>

            <div class="postButtonContainer">
                <button type="submit" class="btn px-4 postButton">Post</button>
                <P class="text-center text-danger"><?php echo $error_message ?> </P>
            </div>

        </form>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>