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

$personDp='';
$personEmail = '';
$messages = array();
if (isset($_GET['personName']) && isset($_GET['personEmail']) && isset($_GET['personDp'])){
    $personName = $_GET['personName'];
    $personEmail = $_GET['personEmail'];
    $personDp = $_GET['personDp'];

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $statement = $pdo->prepare("SELECT message as text,from_id, isPost,postId FROM chats WHERE from_id IN('$user_email', '$personEmail') and to_id IN('$user_email', '$personEmail') ORDER BY chat_id ASC ;");
    $statement->execute();
    $messages = $statement->fetchALL(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../CSS/chat.css">

    <title>Chat</title>
</head>

<body>
    <?php include_once "../Helper/header.php"?>

    <section class="chatContainer">
        <!-- chat person details -->
        <div class="flex justify-start" id="chatPersonInfo">
            <img id="chatAblePersonDp" src="<?php echo $personDp;?>" alt="Person DP">
            <h3 id="chatAblePersonName" class="ml-1 text-xl"><?php echo $personName; ?></h3>
        </div>

        <!-- messages -->
        <div class="scroll mt-2" id="chatBox">
<!--            <p class="left">Lorem ipsum dolor sit amet consectetur.</p> <br>-->
<!--            <p class="right">Lorem ipsum dolor sit amet consectetur adipisicing elit. Officiis, voluptatem!</p> <br>-->

            <?php foreach ($messages as $message):  ?>
                <?php if ($message['from_id'] == $user_email): ?>
                    <p class="right chat"><?php echo $message['text']; ?></p> <br>
                <?php endif; ?>
                <?php if ($message['from_id'] != $user_email): ?>
                    <?php if ($message['isPost'] == 1): ?>
                        <?php
                        $postId = $message['postId'];
                        $statement = $pdo->prepare("SELECT title from post where post_id='$postId';");
                        $statement->execute();
                        $postInfo = $statement->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <p class="left chat"><span class="yellow_bg"><?php echo $postInfo['title'].'<br>'; ?></span> <?php echo $message['text']; ?></p> <br>
                        <?php continue; ?>
                    <?php endif; ?>
                    <p class="left chat"><?php echo $message['text']; ?></p> <br>
                <?php endif; ?>
            <?php endforeach; ?>

        </div>

        <!-- send sms -->
        <div class="flex justify-start mt-2 sendOption">
            <input type="text" id="smsInput">
            <button id="sendBtn">Send</button>
        </div>

    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        const scrollDown = () => {
            const chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        };
        scrollDown();

        // send message
        $(document).ready(function() {
           $("#sendBtn").on('click', function(){
               message = $("#smsInput").val();
               if (message == ''){
                   return;
               }
               to = '<?php echo $personEmail; ?>'
               console.log(to);
               // $("#smsInput").val('');
               $.post("../Ajax/insert.php",
                   {
                       message: message,
                       to_id: to
                   },
                   function(data, status) {
                       $("#smsInput").val('');
                       $("#chatBox").append(data);
                   }
               );
           });
        });

        // auto refresh / reload
        let fetchData = () =>{
            const userEmail = "<?php echo $user_email; ?>";
            const personEmail = "<?php echo $personEmail ; ?>";
            if (personEmail === "") return;

            $.post("../Ajax/getMessage.php",
                {
                    user: userEmail,
                    person: personEmail
                },
                function (data, status){
                    if (status){
                        document.getElementById("chatBox").innerHTML = "";
                        $("#chatBox").append(data);
                        scrollDown();
                    }
                }
            )
        }

        fetchData();
        setInterval(fetchData, 1000);

    </script>
</body>

</html>