<?php
session_start();

require_once(__DIR__ . '/../database/users.php');
require_once(__DIR__ . '/../database/client.class.php');
require_once(__DIR__ . '/../database/connection.db.php');

$_POST['username'] = strtolower($_POST['username']);
$_POST['email'] = strtolower($_POST['email']);

$errors = validateRegister();

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['register_values'] = retrieveFormFields();
    header('Location: /pages/register.php');
    exit;
}

if (createAccount($_POST['name'], $_POST['username'], $_POST['email'], $_POST['password1'])){
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));

    $file = $_FILES['profile-input']['name'];

    if ($file != "") {
        $path = pathinfo($file);
        $extension = $path['extension'];
        $dir = __DIR__ . "/../images/";
        $db = getDatabaseConnection();
        $filename = Client::getUserId($db, $_POST['username']);
        $temp = $_FILES['profile-input']['tmp_name'];
        $name = $dir . $filename . '.' . $extension;


        move_uploaded_file($temp, $name);
    }

    header('Location: /');
} else {
    $errors['undefined'] = 'Something went wrong.';
    $_SESSION['errors'] = $errors;
    $_SESSION['register_values'] = retrieveFormFields();
    header('Location: /pages/register.php');
}

?>