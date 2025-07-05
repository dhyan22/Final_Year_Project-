<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login_signup.php");
    exit();
}

if (!isset($_SESSION["quiz_questions"])) {
    $language = $_SESSION["quiz_language"] ?? 'English';
    $difficulty = $_SESSION["quiz_difficulty"] ?? 'Easy';
    
    $stmt = $conn->prepare("SELECT * FROM questions WHERE language = ? AND difficulty = ? ORDER BY RAND() LIMIT 5");
    $stmt->bind_param("ss", $language, $difficulty);
    $stmt->execute();
    $_SESSION["quiz_questions"] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    $_SESSION["current_question"] = 0;
    $_SESSION["score"] = 0;
    $_SESSION["correct_answers"] = 0;
    $_SESSION["wrong_answers"] = 0;
    $_SESSION["total_question_count"] = count($_SESSION["quiz_questions"]);
}

$questions = $_SESSION["quiz_questions"];
$currentQuestionIndex = $_SESSION["current_question"];

if ($currentQuestionIndex >= count($questions)) {
    header("Location: result.php");
    exit();
}

$question = $questions[$currentQuestionIndex];

$feedback = '';
$isCorrect = null;
if (isset($_SESSION['answer_feedback'])) {
    $feedback = $_SESSION['answer_feedback']['message'];
    $isCorrect = $_SESSION['answer_feedback']['is_correct'];
    unset($_SESSION['answer_feedback']); 
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Interface</title>
    <link rel="stylesheet" href="quiz_interface.css">
</head>
<body>
    <?php if ($feedback): ?>
    <div class="feedback-popup <?= $isCorrect ? 'correct-feedback' : 'wrong-feedback' ?>">
        <?= $feedback ?>
    </div>
    <?php endif; ?>

    <div class="quiz-interface">
        <div class="timer" id="timer">Time Left: 15</div>
        <h2>Question <?= $currentQuestionIndex + 1 ?> of <?= count($questions) ?></h2>
        <div class="question"><?= htmlspecialchars($question["question"]) ?></div>

        <form id="quizForm" class="options" action="submit_quiz.php" method="POST">
            <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
            <?php for ($i = 1; $i <= 4; $i++): ?>
                <button type="submit" name="selected_option" value="<?= $i ?>">
                    <?= htmlspecialchars($question["option$i"]) ?>
                </button>
            <?php endfor; ?>
        </form>
    </div>

    <script>
    let timeLeft = 15;
    let timer;

    function startTimer() {
        timer = setInterval(() => {
            timeLeft--;
            document.getElementById("timer").innerText = `Time Left: ${timeLeft}`;
            if (timeLeft <= 0) {
                clearInterval(timer);
                document.getElementById("quizForm").submit();
            }
        }, 1000);
    }

    <?php if ($currentQuestionIndex + 1 < count($questions)): ?>
    document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "hidden") {
            alert("You switched tabs. The quiz will be submitted automatically.");
            endQuiz();
        }
        function endQuiz() {
            clearInterval(timer);
            alert(`Quiz Over! Your score is ${<?= $_SESSION["score"] ?>}`);
            window.location.href = "result.php";
        }
    });
    <?php endif; ?>

    startTimer();
    
    setTimeout(() => {
        const popup = document.querySelector('.feedback-popup');
        if (popup) popup.style.display = 'none';
    }, 2500);
</script>
</body>
</html>

