<?php
session_start();

require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/ticket.class.php');

if(!isset($_SESSION['username'])){
   die(header("Location: /pages/login.php"));
}

if ($_SESSION['csrf'] !== $_POST['csrf']) {
    die(header('Location: /pages/ticket.php?id=' . $_POST['ticketId']));
}

$db = getDatabaseConnection();
Ticket::addComment(
    $db,
    $_POST['ticketId'],
    $_SESSION['username'],
    $_POST['text'],
    is_string($_POST['faq']) ? $_POST['faq'] : ''
);

header('Location: /pages/ticket.php?id=' . $_POST['ticketId']);

?>