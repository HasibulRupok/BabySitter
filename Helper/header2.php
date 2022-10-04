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
                <div class="flex items-center md:order-2">
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