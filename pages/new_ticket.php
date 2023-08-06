<?php
    session_start();

    if(!isset($_SESSION['username'])){
        die(header("Location: /pages/login.php"));
    }
    
    require_once(__DIR__ . '/../templates/common.php');
    require_once(__DIR__ . '/../templates/ticket.php');

    output_header(true);

    new_ticket_form();

    output_footer();

unset($_SESSION['new_ticket_error']);
?>