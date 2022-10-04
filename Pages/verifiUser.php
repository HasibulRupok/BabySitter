<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

$user =  require_once "../Helper/loadUser.php";
$dp = $user['dp'];
$email = $_SESSION['userEmail'];
$profession = '';
$description = '';
$error = '';
$image_path = 'jdfnadskfj';
$image = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'){

    $image = $_FILES['nid'] ?? null;

    if ($image && $image['tmp_name']) {
        $image_path = 'NID/' . randomDirectory(8) . '/' . $image['name'];
        mkdir(dirname($image_path));

        move_uploaded_file($image["tmp_name"], $image_path);


    }
//    echo "<pre>";
//    var_dump($image_path);
//    echo "</pre>";

    $profession = $_POST['profession'];
    $description = $_POST['about'];

    if ($description == '' && $profession == ''){
        $error = "Everything is required";
    }
    else{
        $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $statement = $pdo->prepare("INSERT INTO verifiedUser (email, description, profession, nidLink) VALUES (:email, :description, :profession, :nidLink)");
        $statement->bindValue(':email', $email);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':profession', $profession);
        $statement->bindValue(':nidLink', $image_path);

        $statement->execute();

        header("Location: profile.php");
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css">
    <!--    font-awesome -->
    <link rel="stylesheet" href="../CSS/all.css">
    <link rel="stylesheet" href="../CSS/verifuUser.css">
    <link rel="stylesheet" href="../CSS/header.css">
    <title>Verify on BabySitter</title>
</head>

<body>
<!--    --><?php //require_once "../Helper/header.php" ?>
    <h3 class="text-center" id="topHeader">Hello <?php echo $user['last_name'];  ?>
<!--        <i class="fa-solid fa-circle-check" id="blue-check"></i>-->
    </h3>
    <p class="text-center" id="topParagraph">For your account verification you need to put all the details below</p>

    <div class="userImageContainer">
        <img class="userImage" src="<?php echo $user['dp'] ;  ?>" alt="">
    </div>

    <section class="formContainer">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="disabledTextInput" class="form-label">Your name</label>
                <input type="text" disabled id="disabledTextInput" name="name" class="form-control" placeholder="Name"
                    value="<?php echo $user['first_name'].' '.$user['last_name']; ?>">
            </div>

            <div class="mb-3">
                <label for="disabledTextInput" class="form-label">Your email</label>
                <input type="text" id="disabledTextInput" name="email" class="form-control" placeholder="Disabled input"
                    disabled value="<?php echo $user['email'] ?>">
            </div>

            <div>
                <label class="form-label">Your profession</label>
                <input class="form-control" type="text" value="<?php echo $profession ?>" placeholder="Your profession" name="profession"
                    aria-label="default input example">
            </div>

            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">About yourself</label>
                <textarea class="form-control" name="about" id="exampleFormControlTextarea1" rows="3"><?php echo $description ?></textarea>
            </div>

            <div class="mb-3">
                <label for="formFile" class="form-label">Upload your NID</label>
                <input name="nid" class="form-control" type="file" id="formFile">
            </div>

            <div id="verifyBtnContainer">
                <button class="verifyButton" type="submit">Apply for verification</button>
                <p class="text-center text-danger"><?php echo $error ?></p>
            </div>

        </form>
    </section>




    <script src="	https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>