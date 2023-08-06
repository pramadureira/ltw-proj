<?php
    declare(strict_types = 1);

    session_start();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/department.php');

    if(!isset($_SESSION['username'])){
        header("Location: /pages/login.php");
    } else {
        $db = getDatabaseConnection();

        $department_agents = getDepartmentAgents($_GET['department']);

        echo json_encode($department_agents);        
    }
?>