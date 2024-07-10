<?php session_start() ?>
<?php $_SESSION["logged_in"] = false ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script defer src="../js/redirect.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
    <title>QuizIT! | Login</title>
</head>
<body>
<main id="container" class="container animate__animated animate__bounceIn">
    <h1>Login to QuizIT!</h1>
    <p>Welcome to an exciting quiz experience!</p>
    <form id="input-form" method="post" action="dashboard_authenticator.php">
        <input class="input-data" type="text" name="username" placeholder="Username" required autofocus
               autocomplete="on">
        <input class="input-data" type="password" name="password" placeholder="Password" required autocomplete="on">
        <input class="input-data" type="submit" value="Login">
    </form>
    <p id="error">
        <?php
        if (isset($_REQUEST["credential-error"])) {
            echo "Credentials are not correct, please try again0.";
        } else if (isset($_REQUEST["database-error"])) {
            echo "A problem with the database has occurred, please try again.";
        }
        ?>
    </p>
    <form id="redirect-form" action="signup.php" method="post">
        Don't have an account? <input type="submit" id="redirect-link" value="Sign Up">now!
    </form>
</main>
</body>
</html>
<?php include 'end_session.php' ?>
