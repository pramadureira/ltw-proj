<?php
    session_start();

    if(!isset($_SESSION['username'])){
        die(header("Location: /pages/login.php"));
    }

    require_once(__DIR__ . '/../templates/common.php');
    require_once(__DIR__ . '/../templates/users.php');
    $user = getUserInfo($_SESSION['username']);

    if (!$user['isAdmin']) {
        header("Location: /pages/index.php");
    }

    output_header(true);?>

    <section id="management"><?php
        output_control();
        output_users();
    ?></section><?php

    output_footer();
?>