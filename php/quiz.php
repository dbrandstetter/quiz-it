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
    <title>QuizIT! | Quiz</title>
</head>
<body>
<?php include 'header.php' ?>
<main>
    <section id="main-container" class="container-stable">
        <h1>Quiz: <?php echo $_GET["title"]; ?></h1>
    </section>
    <section id="quiz-container" class="container-stable">
        <div class="quiz-grid">
            <?php
            $servername = "localhost";
            $account = "root";
            $credentials = "";
            $dbname = "QuizIT";
            $user_id = $_GET['user_id'];
            $quiz_id = $_GET['quiz_id'];
            $title = urldecode($_GET['title']);
            $index = $_GET['index'];
            $answers = null;

            if (isset($_SESSION["answers"])) {
                $answers = $_SESSION["answers"];
            } else {
                $answers = array();
            }

            try {
                $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $characteristics = $connection->prepare('
                                                SELECT MIN(PK_questionID) AS min_question_id, COUNT(*) AS question_count
                                                FROM Quiz 
                                                    LEFT OUTER JOIN Question ON Quiz.PK_quizID = Question.FK_PK_quizID 
                                                   WHERE PK_quizID = :quiz_id
                                                ORDER BY PK_questionID
            ');
                $characteristics->bindParam(':quiz_id', $quiz_id);
                if ($characteristics->execute()) {
                    $characteristics = $characteristics->fetch();

                    if ($characteristics["question_count"] == 0) {
                        echo "<h2>No questions available for this quiz.</h2>";
                        exit;
                    }
                }

                if ($index != 0) {
                    echo "<div id='previousForm'>";
                    echo "<form method=\"post\" action=\"quiz.php?user_id=" . $user_id . "&quiz_id=" . $quiz_id . "&title=" . urlencode($title) . "&index=" . ($index - 1) . "\">";
                    echo "<button type=\"submit\">Previous question</button>";
                    echo "</form>";
                    echo "</div>";
                }

                echo "<div id='question-count'>";
                echo "<h2>Question " . ($index + 1) . " of " . $characteristics["question_count"] . "</h2>";
                echo "</div>";

                if ($index != $characteristics["question_count"] - 1) {
                    echo "<div id='nextForm'>";
                    echo "<form method=\"post\" action=\"quiz.php?user_id=" . $user_id . "&quiz_id=" . $quiz_id . "&title=" . urlencode($title) . "&index=" . ($index + 1) . "\">";
                    echo "<button type=\"submit\">Next question</button>";
                    echo "</form>";
                    echo "</div>";
                }

                $quizzes = $connection->prepare('
                                                SELECT PK_questionID AS question_id, Question.text AS question_text, type, PK_answerID AS answer_id, Answer.text AS answer_text, correct 
                                                FROM Quiz 
                                                    LEFT OUTER JOIN Question ON Quiz.PK_quizID = Question.FK_PK_quizID 
                                                    LEFT OUTER JOIN Answer ON Question.PK_questionID = Answer.FK_PK_questionID
                                                WHERE PK_quizID = :quiz_id
                                                    AND PK_questionID = :question_id
                                                ORDER BY Answer.text
            ');
                $quizzes->bindParam(':quiz_id', $quiz_id);
                $question_id = $characteristics["min_question_id"] + $index;
                $quizzes->bindParam(':question_id', $question_id);

                if ($quizzes->execute()) {
                    $firstCycle = true;
                    echo "<div id='answer'>";
                    echo "<form id=\"input-form save-answer-form\" method=\"post\" action=\"save_answer.php?user_id=" . $user_id . "&quiz_id=" . $quiz_id . "&title=" . urlencode($title) . "&index=" . $index . "\">";
                    echo "<input type=\"hidden\" name=\"user_id\" value=\"" . $user_id . "\">";
                    echo "<input type=\"hidden\" name=\"quiz_id\" value=\"" . $quiz_id . "\">";
                    echo "<input type=\"hidden\" name=\"question_id\" value=\"" . $question_id . "\">";
                    echo "<input type=\"hidden\" name=\"index\" value=\"$index\">";

                    foreach ($quizzes as $quiz) {
                        if ($firstCycle) {
                            echo "<h3>" . $quiz['question_text'] . "</h3>";
                            $firstCycle = false;
                        }

                        switch ($quiz["type"]) {
                            case "radio":
                                echo "<br>";
                                echo "<label for='" . $quiz["answer_id"] . "'>";
                                if (isset($answers[$question_id]) && $answers[$question_id] == $quiz["answer_id"]) echo "<input name=\"" . $quiz["question_id"] . "\" id='" . $quiz["answer_id"] . "' type=\"radio\" value=\"" . $quiz["answer_text"] . "\" checked>"; else
                                    echo "<input name=\"" . $quiz["question_id"] . "\" id='" . $quiz["answer_id"] . "' type=\"radio\" value=\"" . $quiz["answer_text"] . "\">";
                                echo $quiz["answer_text"] . "</label>";
                                break;

                            case "checkbox":
                                echo "<br>";
                                echo "<label for='" . $quiz["answer_id"] . "'>";
                                if (isset($answers[$question_id]) && isset($answers[$question_id][$quiz["answer_id"]])) echo "<input name=\"" . $quiz["answer_id"] . "\" id='" . $quiz["answer_id"] . "' type=\"checkbox\" value=\"" . $quiz["answer_text"] . "\" checked>"; else
                                    echo "<input name=\"" . $quiz["answer_id"] . "\" id='" . $quiz["answer_id"] . "' type=\"checkbox\" value=\"" . $quiz["answer_text"] . "\">";
                                echo $quiz["answer_text"] . "</label>";
                                break;

                            case "text":
                                echo "<br>";
                                echo "<label for='" . $quiz["question_id"] . "'>Answer: </label>";
                                if (isset($answers[$question_id])) echo "<input id='" . $quiz["question_id"] . "' class='quiz-text-input' type=\"text\" name=\"" . $quiz["question_id"] . "\" value=\"" . $answers[$question_id] . "\" autofocus>"; else
                                    echo "<input id='" . $quiz["question_id"] . "' class='quiz-text-input' type=\"text\" name=\"" . $quiz["question_id"] . "\" placeholder='Type here...' autofocus>";
                                break;
                        }
                    }


                    echo "<br><br><input type=\"submit\" id=\"save-answer\" value=\"Save answer\">";
                    echo "</form>";
                    if ($characteristics["question_count"] == $index + 1) {
                        echo "<form method=\"post\" action=\"quiz_results.php?user_id=" . $user_id . "&quiz_id=" . $quiz_id . "&title=" . urlencode($title) . "\">";
                        echo "<input type=\"submit\" id='save-answer' value='Finish quiz'>";
                        echo "</form>";
                    }

                    if (isset($_SESSION["answer_saved"])) {
                        echo "<br><br><i>Answer saved!</i>";
                        unset($_SESSION["answer_saved"]);
                    }
                    echo "</div>";
                }
                $_SESSION["answers"] = $answers;
            } catch (PDOException $e) {
                header("Location: signup.php?database-error=true");
                exit;
            }
            ?>
        </div>
    </section>
</main>
<?php include 'footer.php' ?>
</body>
</html>
