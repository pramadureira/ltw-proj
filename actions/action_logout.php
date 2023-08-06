<?php
    session_start();

    if(!isset($_SESSION['username'])){
        die(header("Location: /pages/login.php"));
     }
     
     if ($_SESSION['csrf'] !== $_POST['csrf']) {
         die(header('Location: /'));
     }

    session_destroy();

    header('Location: /');
?>