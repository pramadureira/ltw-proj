<?php
    session_start();

    if(!isset($_SESSION['username'])){
        die(header("Location: /pages/login.php"));
    }
    
    require_once(__DIR__ . '/../templates/common.php');
    require_once(__DIR__ . '/../templates/user.php');

    output_header(true);

    output_user_profile();

    output_footer();
?>