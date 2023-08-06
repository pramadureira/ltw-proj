<?php

    require_once(__DIR__ . '/../database/connection.db.php');

    function getDepartments() {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Department ORDER BY name ASC');
        $stmt->execute();
    
        return $stmt->fetchAll();
    }

    function getDepartment($id) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Department WHERE departmentId=?');
        $stmt->execute(array($id));
    
        $res = $stmt->fetch();
        if ($res === false) return null;
        return $res['name'];
    }

    function getDepartmentId($name) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT departmentId FROM Department WHERE name=?');
        $stmt->execute(array($name));
        
        $res = $stmt->fetch();
        if ($res === false) return null;
        return $res['departmentId'];
    }

    function getDepartmentAgents(?string $department) {
        if (is_null($department)) return array();

        $db = getDatabaseConnection();
        
        $departmentId = getDepartmentId($department);

        $stmt = $db->prepare('SELECT username FROM Client WHERE userId IN (SELECT userId FROM AgentDepartment WHERE departmentId=?)');
        $stmt->execute(array($departmentId));

        return $stmt->fetchAll();
    }

    function insertDepartment(string $department) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO Department (name) VALUES (?)');
        $stmt->execute(array($department));
    }
?>