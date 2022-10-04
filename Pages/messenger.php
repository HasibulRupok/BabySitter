<?php
session_start();
if (!isset($_SESSION['userEmail'])) {
    header("Location: ../public/login.php");
    exit();
}

//current user
$user = require_once "../Helper/loadUser.php";
$user_email = $_SESSION['userEmail'];
$dp = $user['dp'];

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$statement = $pdo->prepare("SELECT from_id, to_id FROM chats WHERE from_id = '$user_email' OR to_id = '$user_email'");
$statement->execute();
$allEmail = $statement->fetchAll(PDO::FETCH_ASSOC);

$persons = array();
foreach ($allEmail as $em){
    if ($em['to_id'] != $user_email && $em['from_id'] == $user_email || $em['to_id'] == $user_email && $em['from_id'] != $user_email){
        if ($em['to_id'] == $user_email ){
            $load_able_user = $em['from_id'];
            $statement = $pdo->prepare("SELECT dp, concat_ws(' ', first_name, last_name) as name FROM user_info where email = '$load_able_user'");
            $statement->execute();
            $temp = $statement->fetch(PDO::FETCH_ASSOC);
            $temp[] = $em['from_id'];
        }
        else{
            $load_able_user = $em['to_id'];
            $statement = $pdo->prepare("SELECT dp, concat_ws(' ', first_name, last_name) as name FROM user_info where email = '$load_able_user'");
            $statement->execute();
            $temp = $statement->fetch(PDO::FETCH_ASSOC);
            $temp[] = $em['to_id'];
        }

//        $statement = $pdo->prepare("SELECT dp, concat_ws(' ', first_name, last_name) as name FROM user_info where email = '$em'");
//        $statement->execute();
//        $temp = $statement->fetch(PDO::FETCH_ASSOC);
        if (!in_array($temp, $persons)){
            $persons[] = $temp;
        }

    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../CSS/messanger.css">

    <title>Document</title>
</head>

<body>
    <?php include_once "../Helper/header.php";?>

    <section id="messenger" class="flex justify-center">
        <div id="userList">
            <input type="text" id="searchText" placeholder="Search user...">
            <br>
            <?php foreach ($persons as $person):  ?>

            <form action="chat.php" method="get" enctype="multipart/form-data" class="eachUser">
                <input type="hidden" name="personEmail" value="<?php echo $person['0'] ?>">
                <input type="hidden" name="personName" value="<?php echo $person['name'] ?>">
                <input type="hidden" name="personDp" value="<?php echo $person['dp'] ?>">

                <button type="submit" class="flex" id="">
                    <img src="<?php echo $person['dp'] ?>" alt="User Image" id="chatPersonDP">
                    <h3 class="ml-1 text-xl userName"><?php echo $person['name'] ?></h3>
                </button>
            </form>

            <?php endforeach; ?>

        </div>
    </section>

    <!--search results-->
    <section class=" justify-center" id="showSearchResult">

        <!--  here will show the search result  -->

    </section>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#searchText").on("input", function () {

                const searchText = $("#searchText").val();
                if (searchText == "") {
                    document.getElementById("showSearchResult").innerHTML = "";
                    return;
                }

                $.post("../Ajax/chatSearch.php",
                    {
                        key: searchText
                    },
                    function (data, status) {
                        if (status){
                            if (data){
                                document.getElementById("showSearchResult").innerHTML = "";
                                $("#showSearchResult").append(data);
                            }
                        }
                    }
                );
            })

        })
    </script>
</body>

</html>