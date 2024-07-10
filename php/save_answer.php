<?php
session_start();

$servername = "localhost";
$account = "root";
$credentials = "";
$dbname = "QuizIT";
$user_id = $_POST['user_id'];
$quiz_id = $_POST['quiz_id'];
$index = $_POST['index'];
$question_id = $_POST['question_id'];
$answer = $_POST[$question_id];

try {
    $connection = new PDO("mysql:host=$servername;dbname=$dbname", $account, $credentials);
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the question type according to the question id
    $typeStatement = $connection->prepare('SELECT type FROM Question WHERE PK_questionID = :question_id');
    $typeStatement->bindParam(':question_id', $question_id);

    if ($typeStatement->execute()) {
        $type = $typeStatement->fetch(PDO::FETCH_ASSOC)['type'];
        switch ($type) {
            case "checkbox":
                $answers = $_SESSION["answers"];

                // Get answer ids for the question to update the answer array in the session
                $answerIdsStatement = $connection->prepare('SELECT PK_answerID AS answer_id FROM Answer WHERE FK_PK_questionID = :question_id');
                $answerIdsStatement->bindParam(':question_id', $question_id);

                if ($answerIdsStatement->execute()) {
                    $answerIds = $answerIdsStatement->fetchAll(PDO::FETCH_ASSOC);
                    $correctAnswers = array();
                    $answerArray = array();
                    foreach ($answerIds as $answerId) {
                        if (isset($_POST[$answerId['answer_id']])) {
                            $answerArray[$answerId['answer_id']] = true;
                        }
                    }
                    $_SESSION["answers"][$question_id] = $answerArray;
                }
                break;
            case "radio":
                // Get the answer for this question and then the id of the answer chosen
                $answerStatement = $connection->prepare('SELECT PK_answerID AS answer_id FROM Answer WHERE FK_PK_questionID = :question_id AND text = :answer');
                $answerStatement->bindParam(':question_id', $question_id);
                $answerStatement->bindParam(':answer', $answer);

                if ($answerStatement->execute()) {
                    $answer_id = $answerStatement->fetch(PDO::FETCH_ASSOC)['answer_id'];
                    $_SESSION["answers"][$question_id] = $answer_id;
                }
                break;
            case "text":
                $_SESSION["answers"][$question_id] = $answer;
                break;
        }
    }
} catch (PDOException $e) {
    header("Location: signup.php?database-error=true");
    exit;
}

$_SESSION["answer_saved"] = true;
header("Location: quiz.php?user_id=" . $_POST["user_id"] . "&quiz_id=" . $_POST["quiz_id"] . "&title=" . $_GET["title"] . "&index=" . $index);