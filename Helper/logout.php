<?php

session_start();
unset($_SESSION['userEmail']);
session_destroy();

$url = "../public/login.php";
echo $url;

