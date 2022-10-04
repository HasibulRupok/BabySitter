<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$first_name = "";
$last_name = "";
$email = "";
$password = "";
$password2 = "";
$dob = "";
$nid = "";
$address = "";
$gender = "";
$image_path = '';
$errors = [];

//echo "Req mode is: ".$_SERVER['REQUEST_METHOD']."<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $first_name = $_POST['firstName'];
    $last_name = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $dob = $_POST['birthday'];
    $nid = $_POST['nid'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];


    //checking errors
    $image = $_FILES['image'] ?? null;

    if (!$first_name || !$last_name || !$email || !$password || !$password2 || !$dob || !$nid || !$address || !$image || !$image['tmp_name'] || !$gender){
        echo "<script>alert('Everything is required')</script>";
    }

    if ($image && $image['tmp_name']) {
        $image_path = 'images/' . randomDirectory(8) . '/' . $image['name'];
        mkdir(dirname($image_path));

        move_uploaded_file($image["tmp_name"], $image_path);
    }


//    checking already exist or not
    $statement = $pdo->prepare("SELECT password FROM user_info where email = '$email'");
    $statement->execute();
    $user_password = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user_password){
        echo "<script>alert('Already there exist an account with this email')</script>";
    }
    else{
        if ($password !== $password2){
            echo "<script>alert('Both password must be same')</script>";
        }
        else{
            $password =  sha1($password2);
        }

        // now upload all the info to the database
        $statement = $pdo->prepare("INSERT INTO user_info (email, first_name, last_name, 	dob, nid_number, address, dp, password, gender) VALUES (:email, :first_name, :last_name, :dob, :nid_number, :address, :dp, :password, :gender)");
        $statement->bindValue(':email', $email);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':dob', $dob);
        $statement->bindValue(':nid_number', $nid);
        $statement->bindValue('address', $address );
        $statement->bindValue('dp',  $image_path);
        $statement->bindValue('password', $password );
        $statement->bindValue('gender', $gender);

        $statement->execute();

        header('Location: ../public/login.php');
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
    <title>Sign up to BabySitter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../CSS/signup.css">
</head>

<body class="signup">
    <h3 class="text-center mt-3 topLine">Sign Up into BabySitter</h3>

    <div class="center" id="container">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col">
                    <input type="text" name="firstName" class="form-control" placeholder="First name"
                        aria-label="First name" value="<?php echo $first_name ?>">
                </div>
                <div class="col">
                    <input type="text" name="lastName" class="form-control" placeholder="Last name"
                        aria-label="Last name" value="<?php echo $last_name ?>">
                </div>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label mt-2 margin-left">Email address</label>
                <input type="email" name="email" class="form-control transparentInput" id="exampleFormControlInput1"
                    placeholder="name@example.com" value="<?php echo $email ?>">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label margin-left">Password</label>
                <input type="password" name="password" class="form-control transparentInput" id="exampleInputPassword1" value="<?php echo $password2 ?>">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label margin-left">Re enter your password</label>
                <input type="password" name="password2" class="form-control transparentInput"
                    id="exampleInputPassword1" value="<?php echo $password2 ?>">
            </div>
            <section id="gender-sec">
            <div class="mb-3 display-inline">
                <label for="birthday" class="margin-left margin-right">Date of birth</label>
                <input type="date" id="birthday" class="transparentInput p-1" name="birthday" value="<?php echo $dob ?>">

            </div>


            <div id="" class="display-inlineblock">
                <select class="form-select transparentInput display-inline" name="gender" aria-label="Default select example">
                    <option selected>Select Your Gender</option>
                    <option value="1">Male</option>
                    <option value="2">Female</option>
                    <option value="3">Others</option>
                </select>
            </div>
            </section>



            <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label margin-left">NID Number</label>
                <input class="form-control transparentInput" type="number" name="nid"
                    aria-label="default input example" value="<?php echo $nid ?>">
            </div>
            <div>
                <label for="exampleInputPassword1" class="form-label margin-left">Select your address</label>
                <select class="form-select transparentInput" name="address" aria-label="Default select example">
                    <option selected>Open this select menu</option>
                    <option value="1">Baridhara J block</option>
                    <option value="2">Natunbazar Dhaka-1212</option>
                    <option value="3">United City Dhaka-1212</option>
                    <option value="4">Madaniavenue Dhaka</option>
                    <option value="5">Middle Badda, Dhaka</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="formFile" class="form-label margin-left mt-2">Select your image</label>
                <input class="form-control transparentInput" name="image" type="file" id="formFile">
            </div>
            <div class="signup-btn">
                <button type="submit" id="submitButton" class="py-2 px-5">Signup</button>
            </div>

        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>