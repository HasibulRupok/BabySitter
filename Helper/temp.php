<?php
echo $_POST['message'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="temp.css">
    <title>Document</title>
</head>

<body>
    <div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Temporibus distinctio voluptatibus in ad hic illo
            enim itaque perspiciatis alias voluptates quibusdam eveniet officia quam aspernatur inventore atque
            sapiente, praesentium vitae. Lorem ipsum dolor sit amet consectetur adipisicing elit. Laborum expedita
            explicabo esse, eaque rem asperiores error quod est, debitis ea laudantium dolores sint maxime eveniet
            cumque neque tempore commodi libero.</p>
        <button onclick="knockButtonClicked()">Knock</button>

        <div class="hiddenDiv" id="hiddenDiv">
            <form action="" method="post">
                <input type="text" name="message">
                <button onclick="sendPressed()">Send</button>
            </form>
        </div>

    </div>


    <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Necessitatibus eos ipsa, ad dignissimos ab placeat
        minima repellat vitae sapiente deleniti, voluptatem aut, cumque sed quas! Quisquam eaque magni delectus ipsam.
    </p>


    <script src="temp.js"></script>
</body>
</htm>