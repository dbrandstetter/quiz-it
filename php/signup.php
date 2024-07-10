<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script defer src="../js/redirect.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
    <title>QuizIT! | Sign Up</title>
</head>
<body>
<main id="container" class="container animate__animated animate__bounceIn">
    <h1>Sign Up to QuizIT!</h1>
    <p>Welcome to an exciting quiz experience!</p>
    <form id="input-form" method="post" action="dashboard_authenticator.php">
        <input class="input-data" type="text" name="username" placeholder="Username" required autofocus
               autocomplete="off">
        <input class="input-data" type="password" name="password" placeholder="Password" required autocomplete="off">
        <input class="input-data" type="password" name="password-confirmation" placeholder="Confirm password" required
               autocomplete="off">
        <input class="input-data" type="submit" value="Sign Up">
    </form>
    <p id="error">
        <?php
        if (isset($_REQUEST["username-error"])) {
            echo "This username exists already, please pick a different one.";
        } else if (isset($_REQUEST["password-error"])) {
            echo "The password doesn't match with the password confirmation.";
        } else if (isset($_REQUEST["database-error"])) {
            echo "A problem with the database has occurred, please try again.";
        }
        ?>
    </p>
    <form id="redirect-form" action="login.php" method="post">
        Already have an account? <input type="submit" id="redirect-link" href="login.html" value="Login">now!
    </form>
</main>
</body>
</html>
<?php include 'end_session.php'?>
