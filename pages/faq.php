<?php
session_start();

if(!isset($_SESSION['username'])){
    die(header("Location: /pages/login.php"));
}

require_once(__DIR__ . '/../templates/common.php');
require_once(__DIR__ . '/../templates/faq.php');

output_header(isset($_SESSION['username']));

output_FAQs();

output_footer();
?>