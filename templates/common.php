<?php
    require_once(__DIR__ . '/../database/users.php');
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/client.class.php');
    function output_header(bool $logged_in){ ?>
        <!DOCTYPE html>
        <html lang="en-US">
        <head>
            <title>Tickets</title>    
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <script src="../javascript/script.js" defer></script>
            <script src="../javascript/ticket.js" defer></script>
            <script src="../javascript/new_ticket_tags.js" defer></script>
            <script src="../javascript/ticket_search.js" defer></script>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script src="../javascript/dashboard.js" defer></script>
            <script src="../javascript/priority_slider.js" defer></script>
            <script src="../javascript/dropdown.js" defer></script>
            <script src="../javascript/update_profile.js" defer></script>
            <script src="../javascript/validate_form.js" defer></script>
            <link href="../style/style.css" rel="stylesheet">
            <link href="../style/profile.css" rel="stylesheet">
            <link href="../style/faq.css" rel="stylesheet">
            <link href="../style/forms.css" rel="stylesheet">
            <link href="../style/new-ticket.css" rel="stylesheet">
            <link href="../style/users.css" rel="stylesheet">
            <link href="../style/ticket.css" rel="stylesheet">
            <link href="../style/new-faq.css" rel="stylesheet">
            <link rel="icon" href="../images/icons/favicon.ico">
        </head>
        <body>
            <header>
                <h1><a href="index.php">TICKETS</a></h1>
                <?php if ($logged_in) { 
                    $user = getUserInfo($_SESSION['username']); ?>
                    <a href="faq.php"><h2>FAQ</h2></a>
                    <div class="profile-dropdown">
                        <?php 
                        $db = getDatabaseConnection();
                        $filename = Client::getUserId($db, $user['username']);
                        $results = glob(__DIR__ . "/../images/" . $filename . ".*");
                        if ($results){
                            $path = "/../images/" . $filename . "." . pathinfo($results[0], PATHINFO_EXTENSION); ?>
                            <img src=<?=$path?> alt="user_image">
                        <?php }
                        else{ ?>
                            <img src="/../images/default.jpg" alt="user_image">
                        <?php }?>

                        
                        <div class="profile-dropdown-content">
                            <?php
                            if ($user['isAdmin']) { ?>
                                <a href="control_panel.php">Control Panel
                            <?php } ?>
                            <a href="edit_profile.php">Edit profile</a>
                            <form action="../actions/action_logout.php" method="post">
                                <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>">
                                <button type="submit">Sign out</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </header>
        <?php }

    function output_footer(){ ?>
        <footer>
            <p>&copy; Tickets</p>
        </footer>
        </body>
        </html>
    <?php }
?>