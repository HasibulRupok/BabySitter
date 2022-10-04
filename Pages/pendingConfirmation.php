<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}
$userEmail = $_SESSION['userEmail'];
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$statement = $pdo->prepare("SELECT id,postId, title, email FROM pending_payments INNER JOIN post ON pending_payments.postId = post.post_id WHERE pending_payments.author = '$userEmail';");
$statement->execute();
$pendingPosts = $statement->fetchALL(PDO::FETCH_ASSOC);

//echo "<pre>";
//var_dump($pendingPosts);
//echo "</pre>";
$confirmBtnCounter = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../CSS/pendingConfirmation.css">
    <title>Confirm your service</title>
</head>

<body>
    <?php include_once "../Helper/header2.php"?>
    <h1 class="font-bold text-xl text-center my-2">Confirm Your Service</h1>

    <table class="table-fixed w-2/4 mx-auto">
        <thead>
            <tr>
                <th class="">Post Title</th>
                <th>Confirm Service</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pendingPosts as $pendingPost):  ?>
            <tr class="mb-3">
                <td class="text-center"><?php echo $pendingPost['title']; ?></td>
                <td class="">
                    <button class="block mx-auto bg-sky-600 text-white px-3 py-1 rounded hover:bg-sky-700" id="<?php echo $confirmBtnCounter.'-confirm'; ?>">
                        Confirm
                    </button>
                    <input hidden value="<?php echo $pendingPost['id']; ?>" id="<?php echo $confirmBtnCounter.'-PostId'; ?>">
                </td>
                <?php $confirmBtnCounter++; ?>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <button class="block mx-auto my-3 bg-rose-400 hover:bg-rose-500 text-white px-2 py-1 rounded" id="makeComplain">
        Complaint About Service
    </button>

    <section class="w-2/4 mx-auto hidden mt-9" id="complainContainer">
        <h1 class="font-bold text-lg complainBoxTitle my-2">Make your complaint here</h1>

        <input type="text" name="email" id="" value="<?php echo $userEmail ?>" disabled
            class="px-2 py-1 rounded complainInpShawor mx-2">
        <input type="text" name="" id="complaintTitle" placeholder="Enter your post title"
            class="px-2 py-1 rounded complainInpShawor mx-2">
        <input type="number" name="" id="complaintPhnNo" placeholder="Enter your phone number"
            class="px-2 py-1 rounded complainInpShawor mx-2">
        <br>

        <textarea name="" id="complaintDescription" cols="30" rows="10" class="mt-3 complainInpShawor complainBox p-1"
            placeholder="write your compaint in detail"></textarea>

        <br>
        <button id="complainSubmit"
            class="block SubmitBtn bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Submit</button>
    </section>

    <script src="../JS/pendingConfirmation.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            $('button').on('click',function () {
                let id = $(this).attr('id');
                if (!id.includes('-confirm')){
                    if (id !== 'complainSubmit') return;

                    if (id === 'complainSubmit'){
                        const title = document.getElementById("complaintTitle").value;
                        const phoneNo = document.getElementById("complaintPhnNo").value;
                        const description = document.getElementById("complaintDescription").value;
                        if (!title && !phoneNo && !description) return;

                        //const user = <?php //echo $userEmail; ?>

                        $.post("../Ajax/postComplaint.php",
                            {
                                title: title,
                                phone: phoneNo,
                                desc: description
                            },
                            function (data, status) {
                                if (status){
                                    document.getElementById("complainContainer").style.display = "none";
                                }
                            }
                        );
                        return;
                    }
                }
                const arr = id.split('-');
                id = arr[0];
                const pId = id+'-PostId';
                const PostId = document.getElementById(pId).value;

                $.post("../Ajax/authorConfirmation.php",
                    {
                        id: PostId
                    },
                    function (data, status) {
                        if (status){
                            document.getElementById(id+'-confirm').style.visibility = "hidden";
                        }
                    }
                );

            });
        });
    </script>
</body>

</html>