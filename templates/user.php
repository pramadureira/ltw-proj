<?php
    require_once(__DIR__ . '/../database/users.php');
    require_once(__DIR__ . '/../database/connection.db.php');
    require_once(__DIR__ . '/../database/client.class.php');
    function output_user_profile() {
        $user = getUserInfo($_SESSION['username']) ?>
        <section class="userprofile">
            <h2>Profile</h2>
            <form action="../actions/update_user_info.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?=$_SESSION['csrf']?>">
                <label id="name">
                    Name <input type="text" required name="name" value="<?php echo htmlentities($user['name']); ?>">
                </label>
                <label id="username">
                    Username <input type="text" required name="username" value="<?php echo $user['username']; ?>">
                </label>
                <label id="email">
                    E-mail <input type="email" name="email" value="<?php echo $user['email']; ?>">
                </label>
                <label id="newpassword">
                    Password <input type="password" name="password">
                </label>
                <label id="repeatpassword">
                    Repeat password <input type="password" name="newpassword">
                </label>
                <div id="photo">
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
                    <input type="file" id="profile-input" name="profile-input" accept="image/png,image/jpeg">
                    <label for="profile-input" id="newphoto">Upload photo</label>
                </div>
                <button type="submit">Update info</button>
            </form>
        </section>
    <?php }
?>
