<?php
session_start();

require_once(__DIR__ . '/../database/connection.db.php');

$db = getDatabaseConnection();

if (isset($_GET['faq_id'])) {
    $faqId = $_GET['faq_id'];

    try {
        $db->exec( 'PRAGMA foreign_keys = ON;');
        $stmt = $db->prepare('DELETE FROM FAQ WHERE faqId = ?');
        $stmt->execute([$faqId]);

        header('Location: /pages/faq.php');
    } catch (PDOException $err) {
        header('Location: /pages/faq.php');
    }
}

?>
