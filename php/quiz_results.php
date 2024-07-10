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
        <h1>Result: Quiz - <?php echo $_GET["title"]; ?></h1>
    </section>
    <section id="result-container" class="container-stable">
        <?php
        // Retrieve the answers from the session variable
        $answers = $_SESSION["answers"];
        // sunset($_SESSION["answers"]);

        // Connect to the database
        $servername = "localhost";
        $account = "root";
        $credentials = "";
        $dbname = "QuizIT";
        $user_id = $_GET['user_id'];
        $quiz_id = $_GET['quiz_id'];
        $date = date("Y-m-d H:i:s");
        $score = 0;

        try {
            $db = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        $pointStatement = $db->prepare("SELECT COUNT(*) AS max_points FROM Quiz INNER JOIN Question ON PK_quizID = FK_PK_quizID WHERE PK_quizID = :quiz_id");
        $pointStatement->bindValue(":quiz_id", $quiz_id);
        $pointStatement->execute();
        $questionResult = $pointStatement->fetchAll()[0];
        $max_points = $questionResult["max_points"];

        $allQuestionsStatement = $db->prepare("SELECT * FROM Quiz INNER JOIN Question ON PK_quizID = FK_PK_quizID WHERE PK_quizID = :quiz_id");
        $allQuestionsStatement->bindValue(":quiz_id", $quiz_id);
        $allQuestionsStatement->execute();
        $allQuestions = $allQuestionsStatement->fetchAll();

        // Loop through the answers and mark them as correct or wrong, modifying the score accordingly
        // Also, print the given answers and the correct answers
        foreach ($allQuestions as $question) {
            $type = $question["type"];
            $question_text = $question["text"];
            $question_id = $question["PK_questionID"];

            echo "<h3>Question: " . $question_text . "</h3>";
            switch ($type) {
                case "checkbox":
                    echo "<strong>Answers given:</strong><br>";
                    $answersGiven = $answers[$question_id];

                    foreach ($answersGiven as $answer_id => $answer) {
                        $answerStatement = $db->prepare("SELECT text FROM Answer WHERE PK_answerID = :answer_id");
                        $answerStatement->bindValue(":answer_id", $answer_id);
                        $answerStatement->execute();
                        $answerText = $answerStatement->fetchAll()[0]["text"];
                        echo $answerText . "<br>";
                    }

                    echo "<br><strong>Correct answers:</strong><br>";
                    $correctAnswersStatement = $db->prepare("SELECT text FROM Answer WHERE FK_PK_questionID = :question_id AND correct = 1");
                    $correctAnswersStatement->bindValue(":question_id", $question_id);
                    $correctAnswersStatement->execute();
                    $correctAnswers = $correctAnswersStatement->fetchAll();

                    foreach ($correctAnswers as $correctAnswer) {
                        echo $correctAnswer["text"] . "<br>";
                    }

                    $allAnswersQuerry = $db->prepare("SELECT PK_answerID FROM Answer WHERE FK_PK_questionID = :question_id");
                    $allAnswersQuerry->bindValue(":question_id", $question_id);
                    $allAnswersQuerry->execute();
                    $allAnswers = $allAnswersQuerry->fetchAll();

                    // Test the whole question for correctness and add score points if correct
                    $correct = true;
                    foreach ($allAnswers as $answer) {
                        $answer_id = $answer["PK_answerID"];
                        $answerStatement = $db->prepare("SELECT correct FROM Answer WHERE PK_answerID = :answer_id");
                        $answerStatement->bindValue(":answer_id", $answer_id);
                        $answerStatement->execute();
                        $answerResult = $answerStatement->fetchAll();
                        $correct = isset($answerResult[0]) ? $answerResult[0]["correct"] : null;

                        if ($correct == 1 && !in_array($answer, $answersGiven)) {
                            $correct = false;
                            break;
                        }
                        if ($correct == 0 && in_array($answer, $answersGiven)) {
                            $correct = false;
                            break;
                        }
                    }
                    if ($correct) {
                        $score++;
                        echo "<br><br><i>&check; You answered correctly! &check;</i>";
                    } else {
                        echo "<br><br><i>&cross; You answered wrong! &cross;</i>";
                    }

                    break;
                case
                "radio":
                    $answer_id = $answers[$question_id];
                    $answerStatement = $db->prepare("SELECT text FROM Answer WHERE PK_answerID = :answer_id");
                    $answerStatement->bindValue(":answer_id", $answer_id);
                    $answerStatement->execute();
                    $answer = $answerStatement->fetchAll()[0]["text"];
                    echo "<strong>Answer given:</strong><br>" . $answer;

                    $correctAnswerStatement = $db->prepare("SELECT text FROM Answer WHERE FK_PK_questionID = :question_id AND correct = 1");
                    $correctAnswerStatement->bindValue(":question_id", $question_id);
                    $correctAnswerStatement->execute();
                    $correct = $correctAnswerStatement->fetchAll()[0]["text"];
                    echo "<br><strong>Correct answer:</strong><br>" . $correct;

                    if ($correct == $answer) {
                        $score++;
                        echo "<br><br><i>&check; You answered correctly! &check;</i>";
                    } else {
                        echo "<br><br><i>&cross; You answered wrong! &cross;</i>";
                    }

                    break;
                case "text":
                    $answer = $answers[$question_id];
                    echo "<strong>Answer given:</strong>" . $answer;

                    $correctAnswerStatement = $db->prepare("SELECT text FROM Answer WHERE FK_PK_questionID = :question_id AND correct = 1");
                    $correctAnswerStatement->bindValue(":question_id", $question_id);
                    $correctAnswerStatement->execute();
                    $correct = $correctAnswerStatement->fetchAll()[0]["text"];
                    echo "<br><strong>Correct answer:</strong>" . $correct;

                    if ($correct == $answer) {
                        $score++;
                        echo "<br><br><i>&check; You answered correctly! &check;</i>";
                    } else {
                        echo "<br><br><i>&cross; You answered wrong! &cross;</i>";
                    }
                    break;
                default:
                    echo "Error: Question type not recognized.";
            }
            echo "<br><hr style='width: 69%;'><br>";
        }
        echo "<h3><i style='text-decoration: underline'>Score: " . $score . "/" . $max_points . " Points</i></h3><br>";


        // Insert the quiz results into the statistics table
        $stmt = $db->prepare('INSERT INTO Statistic (score, date, FK_PK_quizID, FK_PK_userID) VALUES (:score, :date, :fk_pk_quiz_id, :fk_pk_user_id)');
        $stmt->bindParam(':score', $score);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':fk_pk_quiz_id', $quiz_id);
        $stmt->bindParam(':fk_pk_user_id', $user_id);
        $stmt->execute();
        ?>
    </section>
</main>
<?php include 'footer.php' ?>
</body>
</html>
