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
    <title>QuizIT! | Quizzes Dashboard</title>
</head>
<body>
<?php include 'header.php' ?>
<main>
    <section id="main-container" class="container animate__animated animate__bounceIn">
        <h1>Quiz Overview</h1>
    </section>
    <section id="quiz-container" class="container animate__animated animate__bounceIn">
        <?php
        unset($_SESSION['answers']);
        $servername = "localhost";
        $account = "root";
        $credentials = "";
        $dbname = "QuizIT";
        $user_id = $_GET['user_id'];
        $topic_id = $_GET['topic_id'];

        try {
            $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $quizzes = $connection->prepare('SELECT PK_quizID AS quiz_id, title, difficulty FROM User LEFT OUTER JOIN User_Topic ON User.PK_userID = User_Topic.FK_PK_userID LEFT OUTER JOIN Topic ON User_Topic.FK_PK_topicID = Topic.PK_topicID LEFT OUTER JOIN Quiz ON Topic.PK_topicID = Quiz.FK_PK_topicID WHERE User.PK_userID = :user_id AND User_Topic.unlocked = 1 AND PK_topicId = :topic_id ORDER BY Quiz.title');
            $quizzes->bindParam(':user_id', $user_id);
            $quizzes->bindParam(':topic_id', $topic_id);

            if ($quizzes->execute()) {
                foreach ($quizzes as $quiz) {
                    echo "<a class=\"element\" href=\"quiz.php?user_id=" . $user_id . "&quiz_id=" . $quiz['quiz_id'] . "&title=" . $quiz["title"] . "&index=0\">";
                    echo "<div>";
                    echo $quiz['title'];
                    echo "<br>";
                    echo "<span class='difficulty'>Difficulty: " . $quiz['difficulty'] . "/3</span>";
                    echo "</div>";
                    echo "</a>";
                }
            }
        } catch (PDOException $e) {
            header("Location: signup.php?database-error=true");
            exit;
        }
        ?>
    </section>
</main>
<?php include 'footer.php' ?>
</body>
</html>
