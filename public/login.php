<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$email = "";
$password = "";

if ($_SERVER['REQUEST_METHOD'] === "POST"){
    $x = "input-email";
//    echo $x;
//    $email = "<script> document.getElementById($x).value; </script>";
    $email = $_POST["email"];
    $password = $_POST["password"];


    $statement = $pdo->prepare("SELECT password FROM user_info where email = '$email'");
    $statement->execute();
    $user_password = $statement->fetch(PDO::FETCH_ASSOC);


    if ($user_password){
        $hashedPass =  sha1($password);
        if ($hashedPass === $user_password["password"]){
            session_start();
            if (isset($_POST['email'])){
                $_SESSION['userEmail'] = $email;
                header('Location: ../Pages/home.php');
//                echo $_SESSION['userEmail'];
            }

        }
        else{
            echo "<style>.errorMessage{visibility: visible !important;} </style>";
        }
    }
    else{
        echo "<style>.errorMessage{visibility: visible !important;} </style>";
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>BabySitter</title>
    <?php require_once "../Helper/headLinks.php" ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/login.css">
</head>

<body class="full-bg">
    <h3 class="fw-bolder text-center tagline ">Your Baby Our Care</h3>
    <div class="center">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" id="input-email" aria-describedby="emailHelp" value="<?php echo $email?>">
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="exampleInputPassword1" value=<?php echo $password?>>
                <p class="margin-left fw-light"><small>Do not have account <a href="../Pages/signup.php">sign up</a> here</small></p>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="exampleCheck1">
                <label class="form-check-label" for="exampleCheck1">Check me out</label>
            </div>
            <div class="login-btn"><button type="submit" class="btn btn-primary px-5 ">Login</button></div>
            <p class="errorMessage">Invalid email or password</p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>