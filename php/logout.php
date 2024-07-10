<?php
session_unset();
session_destroy();
$_SESSION['logged_in'] = false;
unset($_SESSION['answers']);

header('Location: login.php');