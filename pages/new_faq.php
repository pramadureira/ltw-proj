<?php
session_start();

if(!isset($_SESSION['username'])){
    die(header("Location: /pages/login.php"));
}

require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/faq.php');
$user = getUserInfo($_SESSION['username']);

if (!$user['isAgent'] && !$user['isAdmin']) {
    header("Location: /pages/index.php");
}

output_header(true);

new_faq_form();

output_footer();

unset($_SESSION['new_faq_error']);
?>