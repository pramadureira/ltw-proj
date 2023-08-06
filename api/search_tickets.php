<?php

    session_start();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/client.class.php');
    require_once(__DIR__ . '/../database/ticket.class.php');

    $db = getDatabaseConnection();

    $user = Client::getUser($db, $_SESSION['username']); 

    $tickets = Ticket::getTicketsFiltered($db, $_GET['search'], $_GET['agent'], $_GET['department'], $_GET['status'], $_GET['priority'], $user);

    echo json_encode($tickets);
?>