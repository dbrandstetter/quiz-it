<?php

if (!$_SESSION['logged_in']) {
    // include 'logout.php';
    header("Location: login.php");
}