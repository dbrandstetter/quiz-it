<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script defer src="../js/redirect.js"></script>
    <link rel="stylesheet" href="../css/styles.css">
    <title>QuizIT! | Topics Dashboard</title>
</head>
<body>
<?php include 'header.php' ?>
<main>
    <section id="main-container" class="container animate__animated animate__bounceIn">
        <h1>Topic Overview</h1>
    </section>
    <section id="quiz-container" class="container animate__animated animate__bounceIn">
        <?php
        unset($_SESSION['answers']);
        $servername = "localhost";
        $account = "root";
        $credentials = "";
        $dbname = "QuizIT";
        $user_id = $_GET['user_id'];

        try {
            $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $topics = $connection->prepare('SELECT PK_topicID AS topic_id, topicname FROM User LEFT OUTER JOIN User_Topic ON User.PK_userID = User_Topic.FK_PK_userID LEFT OUTER JOIN Topic ON User_Topic.FK_PK_topicID = Topic.PK_topicID WHERE User.PK_userID = :user_id AND User_Topic.unlocked = 1 ORDER BY User_Topic.completed DESC, Topic.topicname');
            $topics->bindParam(':user_id', $user_id);

            if ($topics->execute()) {
                foreach ($topics as $topic) {
                    echo "<a  class=\"element\" href=\"dashboard_quizzes.php?user_id=" . $user_id . "&topic_id=" . $topic['topic_id'] . "\">";
                    echo    $topic['topicname'];
                    echo "</a>";
                }
            }
        } catch (PDOException $e) {
            header("Location: signup.php?database-error=true");
        }
        ?>
    </section>
</main>
<?php include 'footer.php' ?>
</body>
</html>
