<?php
function connect_to_db() {
    $servername = "localhost";
    $account = "root";
    $credentials = "";
    $dbname = "QuizIT";

    try {
        $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $_SESSION['connection'] = $connection;
        session_write_close();
    } catch (PDOException $e) {
        header("Location: signup.php?database-error=true");
        exit;
    }
}