<?php
    declare(strict_types = 1);

    session_start();

    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/department.php');
    require_once(__DIR__ . '/../database/misc.php');

    if(!isset($_SESSION['username'])){
        header("Location: /pages/login.php");
    } else {
        $db = getDatabaseConnection();
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':

                break;
            case 'POST':
                if ($_POST['type'] === 'department') {
                    $dep = $_POST['value'];
                    $deps = getDepartmentId($dep);
                    if ($dep !== "" && is_null($deps)) {
                        insertDepartment($dep);
                        http_response_code(200);
                    } else http_response_code(404);

                } else if ($_POST['type'] === 'hashtag') {
                    $hashtag = $_POST['value'];

                    if ($hashtag != "" && !hashtagExists($hashtag)) {
                        insertHashtag($hashtag);
                        http_response_code(200);
                    } else http_response_code(404);
                } else if ($_POST['type'] === 'status') {
                    $status = $_POST['value'];

                    if ($status != "" && !statusExists($status)) {
                        insertStatus($status);
                        http_response_code(200);
                    } else http_response_code(404);
                } else http_response_code(400);
                break;
        }
    }
?>