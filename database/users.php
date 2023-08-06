<?php

require_once(__DIR__ . '/../database/connection.db.php');

function userExists($username, $password): bool
{
    $db = getDatabaseConnection();

    $stmt = $db->prepare('SELECT * FROM Client WHERE username=?');
    $stmt->execute(array($username));
    $client = $stmt->fetch();

    if ($client === false)
        return false;

    return (password_verify($password, $client['password']));
}

function createAccount($name, $username, $email, $password): bool {
    $db = getDatabaseConnection();

    $stmt = $db->prepare('SELECT * FROM Client WHERE username=?');
    $stmt->execute(array($username));

    if ($stmt->fetch()) return false;
    
    $stmt = $db->prepare('INSERT INTO Client (name, username, email, password) VALUES (?, ?, ?, ?)');
    $stmt->execute(array($name, $username, $email, password_hash($password, PASSWORD_BCRYPT)));

    $id = Client::getUserId($db, $username);
    $stmt = $db->prepare('INSERT INTO Agent (isAgent, userId) VALUES (false, ?)');
    $stmt->execute(array($id));

    $stmt = $db->prepare('INSERT INTO Admin (isAdmin, userId) VALUES (false, ?)');
    $stmt->execute(array($id));

    return true;
}

function getUserInfo($username) {
    $db = getDatabaseConnection();

    $stmt = $db->prepare('SELECT username, name, email, isAgent, isAdmin FROM Client LEFT JOIN Agent using(userId) LEFT JOIN Admin using(userId) WHERE username=?');
    $stmt->execute(array($username));
    return $stmt->fetch();
}

function validateRegister(): array {
    $errors = array();

    $db = getDatabaseConnection();
    $stmt = $db->prepare('SELECT * FROM Client WHERE username=? OR email=?');
    $stmt->execute(array($_POST['username'], $_POST['email']));
    $client = $stmt->fetch();

    if (empty($_POST['name'])) {
        $errors['name'] = "Please enter a name.";
    }

    if (empty($_POST['username'])) {
        $errors['username'] = "Please enter a username.";
    } else if($client && $_POST['username'] == $client['username']) {
        $errors['username'] = "This username is already taken.";
    } else if (!preg_match ("/^[a-zA-Z0-9]+$/", $_POST['username'])) {
        $errors['username'] = "Username must only contain letters or numbers.";
    }

    if (empty($_POST['email'])) {
        $errors['email'] = "Please enter an email.";
    } else if ($client && $_POST['email'] == $client['email']) {
        $errors['email'] = "This email is already in use.";
    } else if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) === false) {
        $errors['email'] = "Invalid email format.";
    }

    if (empty($_POST['password1'])) {
        $errors['password1'] = "Please enter a password.";
    } else if (strlen($_POST['password1']) < 7) {
        $errors['password1'] = "Password should be at least 7 characters long.";
    }

    if ($_POST['password1'] != $_POST['password2']) {
        $errors['password2'] = "Passwords did not match.";
    }

    return $errors;
}

function validateLogin(): array {
    $errors = array();

    if (empty($_POST['username'])) {
        $errors['username'] = "Please enter a username.";
    }

    if (empty($_POST['password'])) {
        $errors['password'] = "Please enter a password.";
    }

    return $errors;
}

function retrieveFormFields(): array {
    $form_fields = array();

    $form_fields['name'] = $_POST['name'] ?? '';
    $form_fields['username'] = $_POST['username'] ?? '';
    $form_fields['email'] = $_POST['email'] ?? '';
    return $form_fields;
}

?>