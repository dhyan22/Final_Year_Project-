<?php
session_start();
include "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login_signup.php");
    exit();
}

$query = "SELECT role FROM users WHERE id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user["role"] !== "admin") {
    header("Location: index.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $question = $_POST["question"];
    $option1 = $_POST["option1"];
    $option2 = $_POST["option2"];
    $option3 = $_POST["option3"];
    $option4 = $_POST["option4"];
    $correct_option = $_POST["correct_option"];
    $language = $_POST["language"];
    $difficulty = $_POST["difficulty"];

    $query = "INSERT INTO questions (question, option1, option2, option3, option4, correct_option, language, difficulty)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssss", $question, $option1, $option2, $option3, $option4, $correct_option, $language, $difficulty);
    $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $id = $_POST["id"];
    $query = "DELETE FROM questions WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$edit_question = null;
if (isset($_GET["edit_id"])) {
    $id = $_GET["edit_id"];
    $query = "SELECT * FROM questions WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_question = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $question = $_POST["question"];
    $option1 = $_POST["option1"];
    $option2 = $_POST["option2"];
    $option3 = $_POST["option3"];
    $option4 = $_POST["option4"];
    $correct_option = $_POST["correct_option"];
    $language = $_POST["language"];
    $difficulty = $_POST["difficulty"];

    $query = "UPDATE questions SET question=?, option1=?, option2=?, option3=?, option4=?, correct_option=?, language=?, difficulty=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssi", $question, $option1, $option2, $option3, $option4, $correct_option, $language, $difficulty, $id);
    $stmt->execute();

    header("Location: admin.php");
    exit();
}

$query = "SELECT * FROM questions";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Manage Questions</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

<h2>Admin Panel - Manage Questions</h2>

<h3>Add a New Question</h3>
<form method="post">
    <input type="text" name="question" placeholder="Question" required><br>
    <input type="text" name="option1" placeholder="Option 1" required><br>
    <input type="text" name="option2" placeholder="Option 2" required><br>
    <input type="text" name="option3" placeholder="Option 3" required><br>
    <input type="text" name="option4" placeholder="Option 4" required><br>
    <label>Correct Option (1-4):</label>
    <input type="number" name="correct_option" min="1" max="4" required><br>
    <label>Language:</label>
    <input type="text" name="language" required><br>
    <label>Difficulty:</label>
    <select name="difficulty">
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
    </select><br>
    <button type="submit" name="add">Add Question</button>
</form>

<?php if ($edit_question): ?>
    <h3>Edit Question</h3>
    <form method="post">
        <input type="hidden" name="id" value="<?= $edit_question["id"] ?>">
        <input type="text" name="question" value="<?= $edit_question["question"] ?>" required><br>
        <input type="text" name="option1" value="<?= $edit_question["option1"] ?>" required><br>
        <input type="text" name="option2" value="<?= $edit_question["option2"] ?>" required><br>
        <input type="text" name="option3" value="<?= $edit_question["option3"] ?>" required><br>
        <input type="text" name="option4" value="<?= $edit_question["option4"] ?>" required><br>
        <label>Correct Option (1-4):</label>
        <input type="number" name="correct_option" value="<?= $edit_question["correct_option"] ?>" min="1" max="4" required><br>
        <label>Language:</label>
        <input type="text" name="language" value="<?= $edit_question["language"] ?>" required><br>
        <label>Difficulty:</label>
        <select name="difficulty">
            <option value="easy" <?= $edit_question["difficulty"] == "easy" ? "selected" : "" ?>>Easy</option>
            <option value="medium" <?= $edit_question["difficulty"] == "medium" ? "selected" : "" ?>>Medium</option>
            <option value="hard" <?= $edit_question["difficulty"] == "hard" ? "selected" : "" ?>>Hard</option>
        </select><br>
        <button type="submit" name="update">Update Question</button>
    </form>
<?php endif; ?>

<h3>Existing Questions</h3>
<table>
    <tr>
        <th>Question</th>
        <th>Language</th>
        <th>Difficulty</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row["question"] ?></td>
            <td><?= $row["language"] ?></td>
            <td><?= ucfirst($row["difficulty"]) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row["id"] ?>">
                    <button type="submit" name="delete">Delete</button>
                </form>
                <a href="admin.php?edit_id=<?= $row["id"] ?>">Edit</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<center><button><a style="text-decoration:none" href="index.php">Go Back to Home page</a></button></center>

</body>
</html>