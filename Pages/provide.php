<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
$noti_from ='';
$noti_id = '';
$post_id = '';
if (isset($_POST['reqSender']) && isset($_POST['postId']) && isset($_POST['notificationId'])){
    $noti_from = $_POST['reqSender'];
    $post_id = $_POST['postId'];
    $noti_id = $_POST['notificationId'];

    $pdo = require_once "../Helper/databaseConnector.php";

    $statement = $pdo->prepare("SELECT hour,bdt FROM post where post_id = $post_id;");
    $statement->execute();
    $post= $statement->fetch(PDO::FETCH_ASSOC);
    $amount = $post['bdt'] * $post['hour'];
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
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="../CSS/checkout.css">
    <link rel="stylesheet" href="../CSS/all.css">

    <title>Payment | BabySitter</title>
</head>

<body>

    <section class="checkoutContainer">
        <h3>Confirm your payment here</h3>
        <div class="d-block my-3">
            <div class="custom-control custom-radio">
                <input id="credit" name="paymentMethod" type="radio" class="custom-control-input" checked required>
                <label class="custom-control-label" for="credit"> <i class="fa-brands fa-cc-mastercard cardIcon"></i> </label>
            </div>
            <div class="custom-control custom-radio">
                <input id="debit" name="paymentMethod" type="radio" class="custom-control-input" required>
                <label class="custom-control-label" for="debit"> <i class="fa-brands fa-cc-visa cardIcon"></i> </label>
            </div>
            <div class="custom-control custom-radio">
                <input id="paypal" name="paymentMethod" type="radio" class="custom-control-input" required>
                <label class="custom-control-label" for="paypal"> <i class="fa-brands fa-cc-paypal cardIcon"></i> </label>
            </div>
            <div class="custom-control custom-radio">
                <input id="applePay" name="paymentMethod" type="radio" class="custom-control-input" required>
                <label class="custom-control-label" for="applePay"> <i class="fa-brands fa-cc-apple-pay cardIcon"></i> </label>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cc-name">Name on card</label>
                <input type="text" class="form-control" id="cc-name" placeholder="" required>
                <small class="text-muted">Full name as displayed on card</small>
                <div class="invalid-feedback">
                    Name on card is required
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cc-number">Credit card number</label>
                <input type="text" class="form-control" id="cc-number" placeholder="" required>
                <div class="invalid-feedback">
                    Credit card number is required
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cc-expiration">Expiration</label>
                <input type="text" class="form-control" id="cc-expiration" placeholder="" required>
                <div class="invalid-feedback">
                    Expiration date required
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cc-cvv">CVV</label>
                <input type="text" class="form-control" id="cc-cvv" placeholder="" required>
                <div class="invalid-feedback">
                    Security code required
                </div>
            </div>
        </div>


        <form action="paymentConfirmation.php" method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="cc-expiration">Ammount</label>
                    <input type="number" class="form-control" id="cc-expiration" required value="<?php echo $amount;?>" disabled
                        name="amount">
                </div>
            </div>
            <input type="text" hidden value="<?php echo $noti_from; ?>" name="reqSender">
            <input type="text" hidden value="<?php echo $noti_id; ?>" name="notificationId">
            <input type="text" hidden value="<?php echo $post_id; ?>" name="postId">
            <input type="text" hidden value="<?php echo $amount; ?>" name="amount">
            <button class="bg-success text-white px-3 py-1 rounded confirmBtn">Confirm Payment</button>
        </form>
    </section>



    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"
        crossorigin="anonymous"></script>
</body>

</html>