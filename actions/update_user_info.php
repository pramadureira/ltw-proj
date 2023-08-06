<?php
require_once(__DIR__ . '/../database/connection.db.php');
require_once(__DIR__ . '/../database/client.class.php');

session_start();

if(!isset($_SESSION['username'])){
    die(header("Location: /pages/login.php"));
 }
 
 if ($_SESSION['csrf'] !== $_POST['csrf']) {
     die(header('Location: /pages/new_ticket.php'));
 }

$error = false;
$db = getDatabaseConnection();
$id = Client::getUserId($db, $_SESSION['username']);

$new_name = $_POST['name'];
$error = $new_name == "";

$new_email = strtolower($_POST['email']);
$error = $new_email === "" || !filter_var($new_email, FILTER_VALIDATE_EMAIL);

$new_username = strtolower($_POST['username']);
if (Client::getUserId($db, $new_username) !== false) $error = Client::getUserId($db, $new_username) !== $id;

$new_pass = $_POST['password'];
$new_pass_repeat = $_POST['newpassword'];
if ($new_pass === "" && $new_pass_repeat === "") {
    if (!$error) {
        $_SESSION['username'] = $new_username;
        $stmt = $db->prepare('UPDATE Client SET name=?, email=?, username=? WHERE userId=?');
        $stmt->execute(array($new_name, $new_email, $new_username, $id));
    }
} else {
    if (!$error) {
        $_SESSION['username'] = $new_username;
        $stmt = $db->prepare('UPDATE Client SET name=?, email=?, username=?, password=? WHERE userId=?');
        $stmt->execute(array($new_name, $new_email, $new_username, password_hash($new_pass, PASSWORD_BCRYPT), $id));
    }
}

// profile picture

$file = $_FILES['profile-input']['name'];


if ($file != "") {
    $path = pathinfo($file);
    $extension = $path['extension'];
    $dir = __DIR__ . "/../images/";
    $filename = Client::getUserId($db, $_SESSION['username']);
    $temp = $_FILES['profile-input']['tmp_name'];
    $name = $dir . $filename . '.' . $extension;

    $existing_files = glob($dir. $filename . ".*");
    foreach ($existing_files as $existing_file) unlink($existing_file);
      
    move_uploaded_file($temp, $name);
}

header("Location: /pages/edit_profile.php");

?>