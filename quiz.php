<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login_signup.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION["quiz_language"] = $_POST["language"];
    $_SESSION["quiz_difficulty"] = $_POST["difficulty"];
    $stmt = $conn->prepare("SELECT * FROM questions 
                           WHERE language = ? AND difficulty = ? 
                           ORDER BY RAND() LIMIT 5");
    $stmt->bind_param("ss", $_SESSION["quiz_language"], $_SESSION["quiz_difficulty"]);
    $stmt->execute();
    $_SESSION["quiz_questions"] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $_SESSION["current_question"] = 0;
    $_SESSION["score"] = 0;
    $_SESSION["correct_answers"] = 0;
    $_SESSION["wrong_answers"] = 0;
    $_SESSION["total_question_count"] = count($_SESSION["quiz_questions"]);
}

if (!isset($_SESSION["quiz_questions"])) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Instructions</title>
    <link rel="stylesheet" href="quiz.css">
</head>
<body>
    <div class="quiz-container">
        <h2>Quiz Rules</h2>
        <ol>
            <li>15 seconds per question</li>
            <li>Answers are final</li>
            <li>Points for correct answers</li>
        </ol>
        
        <div class="quiz-meta">
            <p><strong>Language:</strong> <?= htmlspecialchars($_SESSION["quiz_language"]) ?></p>
            <p><strong>Difficulty:</strong> <?= htmlspecialchars($_SESSION["quiz_difficulty"]) ?></p>
            <p><strong>Total Questions:</strong> <?= $_SESSION["total_question_count"] ?></p>
        </div>

        <div class="buttons">
            <button onclick="startQuiz()">Start Quiz</button>
            <button onclick="exitQuiz()">Exit Quiz</button>
        </div>
    </div>

    <script>
        function startQuiz() {
            window.location.href = "quiz_interface.php";
        }
        
        function exitQuiz() {
            fetch('clear_quiz_session.php').then(() => {
                window.location.href = "index.php";
            });
        }
    </script>
</body>
</html>