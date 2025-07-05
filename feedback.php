<?php
include "db.php";
$feedback_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, message, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $name, $email, $message);
        $stmt->execute();

        $feedback_msg = "Thank you for your feedback!";
    } else {
        $feedback_msg = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback</title>
    <link rel="stylesheet" href="feedback.css">
    <style>
        
    </style>
</head>
<body>
    <div class="feedback-container">
        <h2>Submit Feedback</h2>
        <form action="" method="POST">
            <label for="name">Your Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Your Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="message">Feedback:</label>
            <textarea name="message" id="message" rows="5" required></textarea>

            <button type="submit">Submit</button>
        </form>
        <?php if (!empty($feedback_msg)) echo "<p class='message'>$feedback_msg</p>"; ?>

        <button><a style="text-decoration:none" href="index.php">Go Back to Home page</a></button>
    </div>
</body>
</html>
