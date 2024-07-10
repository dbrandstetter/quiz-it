<?php
// Init db connection and variables
session_start();
global $connection;
$servername = "localhost";
$account = "root";
$credentials = "";
$dbname = "QuizIT";

try {
    $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $_SESSION['connection'] = $connection;
} catch (PDOException $e) {
    $_SESSION['logged_in'] = false;
    header("Location: signup.php?database-error=true");
}

$username = $_POST['username'];
$password = $_POST['password'];

// Comes from signup.php
if (isset($_POST['password-confirmation'])) {
    $checkStatement = $connection->prepare('SELECT COUNT(*) AS CNT FROM QuizIT.User WHERE User.username = :username');
    $checkStatement->bindValue(':username', $username);
    $checkStatement->execute();

    $result = $checkStatement->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $count = $result['CNT'];

        if ($count === 0) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if ($password != $_POST['password-confirmation']) {
                header("Location: signup.php?password-error=true");
                exit;
            } else {
                // Add user to db
                $insertStatement = $connection->prepare('INSERT INTO QuizIT.User (username, password, role) VALUES (:username, :password, \'user\')');
                $insertStatement->bindValue(':username', $username);
                $insertStatement->bindValue(':password', $hashedPassword);
                $insertStatement->execute();

                // Get primary key of user
                $selectStatement = $connection->prepare('SELECT PK_userID AS user_id FROM QuizIT.User WHERE User.username = :username');
                $selectStatement->bindValue(':username', $username);
                $selectStatement->execute();

                $user = $selectStatement->fetch(PDO::FETCH_ASSOC);

                $_SESSION['username'] = $username;
                $user_id = $user['user_id'];
                $_SESSION['user_id'] = $user['user_id'];

                // Get topic count
                $topicCountStatement = $connection->prepare('SELECT COUNT(*) AS CNT FROM QuizIT.Topic');
                $topicCountStatement->execute();
                $topicCount = $topicCountStatement->fetch(PDO::FETCH_ASSOC)['CNT'];

                // Allow user full quiz access in User_Topic
                for ($i = 1; $i <= $topicCount; $i++) {
                    $mapStatement = $connection->prepare('INSERT INTO QuizIT.User_Topic (FK_PK_userID, FK_PK_topicID, unlocked, completed) VALUES (:user_id, :topic_id, TRUE, FALSE)');
                    $mapStatement->bindValue(':user_id', $user_id);
                    $mapStatement->bindValue(':topic_id', $i);
                    $mapStatement->execute();
                }

                $_SESSION['logged_in'] = true;
                $test = $_SESSION['logged_in'];
                header("Location: ../php/dashboard_topics.php?user_id=" . urlencode($user_id));
            }
        } else {
            $_SESSION['logged_in'] = false;
            header("Location: signup.php?username-error=true");
        }
    } else {
        $_SESSION['logged_in'] = false;
        header("Location: signup.php?database-error=true");
    }
} else {
    // Comes from login.php
    $checkStatement = $connection->prepare('SELECT password FROM QuizIT.User WHERE User.username = :username');
    $checkStatement->bindValue(':username', $username);
    $checkStatement->execute();

    $result = $checkStatement->fetch(PDO::FETCH_ASSOC);

    // Get primary key of user
    $selectStatement = $connection->prepare('SELECT PK_userID AS user_id FROM QuizIT.User WHERE User.username = :username');
    $selectStatement->bindValue(':username', $username);
    $selectStatement->execute();

    $user = $selectStatement->fetch(PDO::FETCH_ASSOC);

    $_SESSION['username'] = $username;
    $user_id = $user['user_id'];
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['logged_in'] = true;

    if ($result) {
        $storedPassword = $result['password'];

        if (password_verify($password, $storedPassword)) {
            $_SESSION['logged_in'] = true;
            $test = $_SESSION['logged_in'];

            header("Location: ../php/dashboard_topics.php?user_id=" . $user_id);
        } else {
            $_SESSION['logged_in'] = false;
            header("Location: login.php?credential-error=true");
        }
    } else {
        $_SESSION['logged_in'] = false;
        header("Location: login.php?credential-error=true");
    }
}
