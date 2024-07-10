<?php
$user_id = $_GET['user_id'];

echo "<header>";
echo "    <a href=\"dashboard_topics.php?user_id=$user_id\" target=\"_self\">";
echo "        <img id=\"quizit-logo\" src=\"../images/quizit-logo.png\" alt=\"QuizIT! Logo\">";
echo "    </a>";
echo "    <div>";
echo "        <a id=\"logout-link\" href=\"logout.php\" target=\"_self\">Log out</a>";
echo "        <a id=\"statistics-link\" href=\"statistics.php?user_id=$user_id\" target=\"_self\">View statistic</a>";
echo "    </div>";
echo "</header>";
