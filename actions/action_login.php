<?php
session_start();

require_once(__DIR__ . '/../database/users.php');

$_POST['username'] = strtolower($_POST['username']);

$errors = validateLogin();

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['login_values'] = retrieveFormFields();
    header('Location: /pages/login.php');
    exit;
}

if (userExists($_POST['username'], $_POST['password'])){
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['csrf'] = bin2hex(openssl_random_pseudo_bytes(32));
    header('Location: /');
} else {
    $errors['undefined'] = 'Invalid username/password.';
    $_SESSION['errors'] = $errors;
    $_SESSION['login_values'] = retrieveFormFields();
    header('Location: /pages/login.php');
}

?>