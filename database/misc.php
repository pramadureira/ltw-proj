<?php

    require_once(__DIR__ . '/../database/connection.db.php');

    function getHashtags() {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Hashtag');
        $stmt->execute();
    
        return $stmt->fetchAll();
    }

    function hashtagExists(string $name): bool
    {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Hashtag WHERE name = ?');
        $stmt->execute(array($name));
        
        $res = $stmt->fetch();
        if (!$res) return false;
        return true;
    }

    function insertHashtag(string $name) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO Hashtag (name) VALUES (?)');
        $stmt->execute(array($name));
    }

    function getStatuses() {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Status');
        $stmt->execute();
    
        return $stmt->fetchAll();
    }
    
    function getFAQs() {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT faqId, question, answer FROM FAQ');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    function insertStatus(string $status) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare('INSERT INTO Status (name) VALUES (?)');
        $stmt->execute(array($status));
    }

    function statusExists(string $name): bool
    {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT name FROM Status WHERE name = ?');
        $stmt->execute(array($name));
        
        $res = $stmt->fetch();
        if (!$res) return false;
        return true;
    }

    function getAgents() {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT username FROM Client JOIN Agent ON Client.userId=Agent.userId WHERE isAgent=true');
        $stmt->execute();

        return $stmt->fetchAll();
    }
    
    function getFaqId($question) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT faqId FROM FAQ WHERE question=?');
        $stmt->execute(array($question));

        return $stmt->fetch()['faqId'];
    }

    function getFaqQuestion($faqId) {
        $db = getDatabaseConnection();

        $stmt = $db->prepare('SELECT question FROM FAQ WHERE faqId=?');
        $stmt->execute(array($faqId));

        return $stmt->fetch()['question'];
    }
?>