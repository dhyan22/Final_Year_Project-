<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login_signup.php");
    exit();
}

$current_user_id = $_SESSION["user_id"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
    <link rel="stylesheet" href="result.css">
    <style>
        
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <ul>
                <img src="logo.jpeg" alt="" width="50" height="50">
                <li><a href="about.html">About us</a></li>
                <li><a href="index.php">Select Exam</a></li>
                <li><a href="result.php">Result</a></li> 
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php
        if (isset($_GET["id"])) {
            $result_id = (int)$_GET["id"];
            $query = "SELECT * FROM quiz_results WHERE id = ? AND user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $result_id, $current_user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo "<p>Result not found or doesn't belong to you.</p>";
            } else {
                $row = $result->fetch_assoc();
                ?>
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <h2>Your Quiz Result</h2>
                <table>
                    <tr><th>Score</th><td><?= $row["score"] ?>/<?= $row["total_questions"] ?></td></tr>
                    <tr><th>Correct Answers</th><td><?= $row["correct_answers"] ?></td></tr>
                    <tr><th>Wrong Answers</th><td><?= $row["wrong_answers"] ?></td></tr>
                    <tr><th>Language</th><td><?= htmlspecialchars($row["language"] ?? 'Unknown') ?></td></tr>
                    <tr><th>Difficulty</th><td><?= htmlspecialchars($row["difficulty"] ?? 'Unknown') ?></td></tr>
                    <tr><th>Date</th><td><?= date("M d, Y h:i A", strtotime($row["quiz_date"])) ?></td></tr>
                </table>
                <?php
            }
        }
        ?>

        <div class="previous-results">
        <br><br><br>
            <h3>Your Previous Attempts</h3>
            <?php
            $query = "SELECT * FROM quiz_results 
                     WHERE user_id = ? 
                     ORDER BY quiz_date DESC 
                     LIMIT 10";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $current_user_id);
            $stmt->execute();
            $results = $stmt->get_result();

            if ($results->num_rows === 0) {
                echo "<p>No previous attempts found.</p>";
            } else {
                while ($row = $results->fetch_assoc()) {
                    echo '
                    <div class="result-card">
                        <strong>'.htmlspecialchars($row["language"] ?? 'Unknown').' ('.htmlspecialchars($row["difficulty"] ?? 'Unknown').')</strong>
                        <p>Score: '.$row["score"].'/'.$row["total_questions"].'</p>
                        <small>'.date("M d, Y h:i A", strtotime($row["quiz_date"])).'</small>
                        <a href="result.php?id='.$row["id"].'">View Details</a>
                    </div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>