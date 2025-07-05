<?php
session_start();
include "db.php";

if (!isset(
    $_SESSION['user_id'],
    $_SESSION['quiz_language'],
    $_SESSION['quiz_difficulty'],
    $_SESSION['quiz_questions'],
    $_SESSION['current_question']
)) {
    session_unset();
    session_destroy();
    die("Session expired. <a href='index.php'>Start new quiz</a>");
}

$questionId = (int)$_POST['question_id'];
$selectedOption = (int)$_POST['selected_option'];

$stmt = $conn->prepare("SELECT correct_option FROM questions WHERE id = ?");
$stmt->bind_param("i", $questionId);
$stmt->execute();
$correctOption = (int)$stmt->get_result()->fetch_assoc()['correct_option'];

$isCorrect = ($selectedOption === $correctOption);
$_SESSION['answer_feedback'] = [
    'message' => $isCorrect ? 'Correct! 🎉' : 'Wrong answer 😢',
    'is_correct' => $isCorrect
];

if ($isCorrect) {
    $_SESSION['score']++;
    $_SESSION['correct_answers']++;
} else {
    $_SESSION['wrong_answers']++;
}

$_SESSION['current_question']++;

if ($_SESSION['current_question'] >= $_SESSION['total_question_count']) {
    $stmt = $conn->prepare("INSERT INTO quiz_results 
        (user_id, language, difficulty, score, total_questions, correct_answers, wrong_answers, quiz_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param(
        "issiiii",
        $_SESSION['user_id'],
        $_SESSION['quiz_language'],
        $_SESSION['quiz_difficulty'],
        $_SESSION['score'],
        $_SESSION['total_question_count'],
        $_SESSION['correct_answers'],
        $_SESSION['wrong_answers']
    );
    $stmt->execute();

    $resultId = $conn->insert_id;

    unset(
        $_SESSION['quiz_questions'],
        $_SESSION['current_question'],
        $_SESSION['score'],
        $_SESSION['correct_answers'],
        $_SESSION['wrong_answers'],
        $_SESSION['quiz_language'],
        $_SESSION['quiz_difficulty']
    );

    header("Location: result.php?id=" . $resultId);
    exit();
}

header("Location: quiz_interface.php");
exit();
?>
