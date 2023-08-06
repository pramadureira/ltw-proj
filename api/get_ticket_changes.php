<?php
    declare(strict_types = 1);

    session_start();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/ticket.class.php');
    require_once(__DIR__ . '/../database/client.class.php');
    require_once(__DIR__ . '/../database/department.php');

    if(!isset($_SESSION['username'])){
        header("Location: /pages/login.php");
    } else {
        $db = getDatabaseConnection();

        $modifications = Ticket::getModifications($db, intval($_GET['ticketId'],10));
        foreach ($modifications as &$modification) {
            $modification['userId'] = Client::getUsername($db, $modification['userId']);
            if ($modification['field'] === 'Agent') {
                if ($modification['old'] !== '') $modification['old'] = Client::getUsername($db, intval($modification['old'], 10));

                if ($modification['new'] !== '') $modification['new'] = Client::getUsername($db, intval($modification['new'], 10));

            } else if ($modification['field'] === 'Department') {
                if ($modification['old'] !== '') $modification['old'] = getDepartment(intval($modification['old'], 10));

                if ($modification['new'] !== '') $modification['new'] = getDepartment(intval($modification['new'], 10));
            }
        }

        echo json_encode($modifications);
    }
?>