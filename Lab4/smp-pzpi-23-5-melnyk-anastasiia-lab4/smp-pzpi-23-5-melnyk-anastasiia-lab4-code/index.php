<?php
require_once 'config.php';
require_once 'credentials.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page === 'logout') {
    unset($_SESSION['user_login']);
    unset($_SESSION['login_time']); 
    header('Location: index.php');
    exit;
}

include 'header.php';

if (!checkUserAuth() && $page !== 'login') {
    require_once 'page404.php';
} else {
    switch ($page) {
        case "home":
            require_once "main.php";
            break;
        case "products":
            require_once "products.php";
            break;
        case "cart":
            require_once "cart.php";
            break;
        case "profile":
            require_once "profile.php";
            break;
        case "login":
            require_once "login.php";
            break;
        default:
            require_once "page404.php";
            break;
    }
}

include 'footer.php';
?>