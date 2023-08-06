<?php
    session_start();

    if(isset($_SESSION['username'])){
        die(header("Location: /"));
    }

    require_once(__DIR__ . '/../templates/common.php');

    output_header(false);
?>

<section id="login">
    <h1>Login</h1>
    <form action="../actions/action_login.php" method="post">
    <label>
        Username <input type="text" name="username" value=<?=$_SESSION['login_values']['username']?? ''?>>
        <span class="error"><?=$_SESSION['errors']['username']?? ''?></span>
    </label>
    <label>
        Password <input type="password" name="password">
        <span class="error"><?=$_SESSION['errors']['password']?? ''?></span>
        <span class="error"><?=$_SESSION['errors']['undefined']?? ''?></span>
    </label>
    <button type="submit">Login</button>
    </form>
    <span id="not_registered">Don't have an account yet? Click <a href="register.php">here</a> to register!</span>
</section>

<?php
    output_footer();
    unset($_SESSION['errors']);
    unset($_SESSION['login_values']);
?>