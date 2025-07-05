<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login_signup.php");
    exit();
}

$languages = $conn->query("SELECT DISTINCT language FROM questions");
$difficulties = ["easy", "medium", "hard"];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Quiz</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
        <nav class="navbar">
            <div class="nav-left">
                <ul>
                    <img src="logo.jpeg" alt="" width="50" height="50">
                    <li><a href="about.html">About us</a></li>
                    <li><a href="result.php">Result</a></li>
                    <li><a href="feedback.php">Feedback</a></li> 
                    <li><a href="terms.html">Terms</a></li>
                    <li><a href="help.html">Help</a></li>
                    <li><a href="privacy.html">Privacy</a></li>
                    <li><a href="logout.php">Logout</a></li>

                </ul>
            </div>
        </nav>
    <div class="quiz-container">
        <h2>Select Quiz</h2>
        <form action="quiz.php" method="post">
            <label for="language">Language:</label>
            <select name="language" id="language" required>
                <?php while ($row = $languages->fetch_assoc()): ?>
                    <option value="<?= $row["language"] ?>"><?= $row["language"] ?></option>
                <?php endwhile; ?>
            </select>

            <label for="difficulty">Difficulty:</label>
            <select name="difficulty" id="difficulty" required>
                <?php foreach ($difficulties as $difficulty): ?>
                    <option value="<?= $difficulty ?>"><?= ucfirst($difficulty) ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <div class="button">
                <button type="submit">Submit</button>
            </div>

        </form>
        
    </div>
    
</body>
</html>