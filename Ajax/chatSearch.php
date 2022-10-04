<?php

if (isset($_POST['key'])){
    $searchText = '%'.$_POST['key'].'%';

    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=babysitter', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $statement = $pdo->prepare("SELECT concat_ws(' ', first_name, last_name) as name, dp, email FROM user_info WHERE first_name LIKE '$searchText' OR last_name LIKE '$searchText';");
    $statement->execute();
    $persons = $statement->fetchALL(PDO::FETCH_ASSOC);
    ?>

    <?php foreach ($persons as $person):  ?>

            <form action="chat.php" method="get" enctype="multipart/form-data" class="eachUser mx-3.5">
                <input type="hidden" name="personEmail" value="<?php echo $person['email'] ?>">
                <input type="hidden" name="personName" value="<?php echo $person['name'] ?>">
                <input type="hidden" name="personDp" value="<?php echo $person['dp'] ?>">

                <button type="submit" class="flex" id="">
                    <img src="<?php echo $person['dp'] ?>" alt="User Image" id="chatPersonDP">
                    <h3 class="ml-1 text-xl userName"><?php echo $person['name'] ?></h3>
                </button>
            </form>

    <?php endforeach; ?>
    <?php
}
?>
