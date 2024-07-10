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
    <title>QuizIT! | Result</title>
</head>
<body>
<?php include 'header.php' ?>
<main>
    <section id="main-container" class="container-stable">
        <h1>Statistics for
            <?php
            $servername = "localhost";
            $account = "root";
            $credentials = "";
            $dbname = "QuizIT";
            $user_id = $_GET['user_id'];

            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $usernameStatement = $pdo->prepare('SELECT username FROM User WHERE PK_userID = :user_id');
                $usernameStatement->bindParam(':user_id', $user_id);
                $usernameStatement->execute();
                $username = $usernameStatement->fetch(PDO::FETCH_ASSOC)['username'];

                echo $username;
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
            ?>
        </h1>
    </section>
    <section id="result-container" class="container-stable">
        <?php
        $servername = "localhost";
        $account = "root";
        $credentials = "";
        $dbname = "QuizIT";
        $user_id = $_GET['user_id'];

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $userStatistics = $pdo->prepare('SELECT * FROM Statistic WHERE FK_PK_userID = :user_id');
            $userStatistics->bindParam(':user_id', $user_id);
            $userStatistics->execute();
            $statistics = $userStatistics->fetchAll(PDO::FETCH_ASSOC);

            $dataStatement = $pdo->prepare('SELECT COUNT(*) AS quiz_count, AVG(score) AS avg_score FROM Statistic WHERE FK_PK_userID = :user_id');
            $dataStatement->bindParam(':user_id', $user_id);
            $dataStatement->execute();
            $data = $dataStatement->fetch(PDO::FETCH_ASSOC);
            $quiz_count = $data['quiz_count'];
            $avg_score = $data['avg_score'];

            // Display the statistics in a table format, centered and with style according to the design
            echo "<h2>Statistics</h2>";
            echo "<p>Number of quizzes taken: " . $quiz_count . "</p>";
            echo "<p>Average score: " . round($avg_score, 2) . "</p><br><br>";

            echo "<hr style='width: 69%'>";

            echo "<h2>History</h2>";
            echo "<table id='statistics-table'>";
            echo "<tr>";
            echo "<th>Topic</th>";
            echo "<th>Quiz</th>";
            echo "<th>Score</th>";
            echo "<th>Date</th>";
            echo "</tr>";
            foreach ($statistics as $statistic) {
                echo "<tr>";
                // Get the topic
                $topicStatement = $pdo->prepare('SELECT topicname FROM Topic INNER JOIN Quiz ON PK_topicID = Quiz.FK_PK_topicID WHERE PK_quizID = :quiz_id');
                $topicStatement->bindParam(':quiz_id', $statistic['FK_PK_quizID']);
                $topicStatement->execute();
                $topic = $topicStatement->fetch(PDO::FETCH_ASSOC)['topicname'];
                echo "<td>" . $topic . "</td>";

                // Get the name of the quiz by its ID
                $quizStatement = $pdo->prepare('SELECT title FROM Quiz WHERE PK_quizID = :quiz_id');
                $quizStatement->bindParam(':quiz_id', $statistic['FK_PK_quizID']);
                $quizStatement->execute();
                $quiz = $quizStatement->fetch(PDO::FETCH_ASSOC)['title'];
                echo "<td>" . $quiz . "</td>";

                // Get the maximum score for the quiz and print in format: 1/n
                $maxScoreStatement = $pdo->prepare('SELECT COUNT(*) AS max_points FROM Quiz INNER JOIN Question ON PK_quizID = FK_PK_quizID WHERE PK_quizID = :quiz_id');
                $maxScoreStatement->bindParam(':quiz_id', $statistic['FK_PK_quizID']);
                $maxScoreStatement->execute();
                $maxScore = $maxScoreStatement->fetch(PDO::FETCH_ASSOC)['max_points'];
                echo "<td>" . $statistic['score'] . "/" . $maxScore . "</td>";
                echo "<td>" . $statistic['date'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";


        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
        ?>
    </section>
</main>
<?php include 'footer.php' ?>
</body>
</html>
