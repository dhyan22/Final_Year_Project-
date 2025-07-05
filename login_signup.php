<?php
session_start();
include "db.php";

$success = "Successfully🌟 Registration✅";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $query = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $firstname = $_POST["firstname"] ?? "";
    $lastname = $_POST["lastname"] ?? "";
    $username = $_POST["username"] ?? "";
    $email = $_POST["email"] ?? "";
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $contact_number = $_POST["contact_number"] ?? "";

    $query = "INSERT INTO users (firstname, lastname, username, email, password, contact_number) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $firstname, $lastname, $username, $email, $password, $contact_number);

    if ($stmt->execute()) {
        $_SESSION["user_id"] = $stmt->insert_id;
        header("Location: login_signup.php");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>



<!DOCTYPE html>
<html>
<head>
    <title>Login / Signup</title>
    <link rel="stylesheet" href="login_signup.css">    
    
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <ul>
                <img src="logo.jpeg" alt="" widht="50" height="50">
                <li><a href="about.html">About us</a></li>
                <li><a href="feedback.php">Feedback</a></li>
                <li><a href="contact.php">Contact US </a></li>
                <li><a href="terms.html">Terms</a></li>
                <li><a href="help.html">Help</a></li>
                <li><a href="privacy.html">Privacy</a></li>
    </nav>
    <br><br><br><br><br><br>
    <div class="wrapper">
        <br><br><br><br><br><br>
        <div class="card-switch">
            <br><br><br><br><br><br><br>
            <label class="switch">
                <input type="checkbox" class="toggle">
                
                <span class="slider"></span>
                <span class="card-side"></span>
                <div class="flip-card__inner"><br>
                    <!-- Login Form -->
                    <div class="flip-card__front">
                        <div class="title">Log in</div>
                        <form class="flip-card__form" method="POST" action="">
                            <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                            <input class="flip-card__input" name="password" placeholder="Password" type="password" required minlength="5">
                            <button class="flip-card__btn" name="login"><div class="button">Let's go!</div></button>
                            <?php if (!empty($error)): ?>
                                <div style="color: red; text-align: center;"><?php echo $error; ?></div>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <!-- Signup Form -->
                    <div class="flip-card__back">
                        <div class="title">Sign up</div>
                        <form class="flip-card__form" method="POST" action="">
                            <input class="flip-card__input" name="firstname" placeholder="First Name" type="text" required>
                            <input class="flip-card__input" name="lastname" placeholder="Last Name" type="text" required>
                            <input class="flip-card__input" name="username" placeholder="Username" type="text" required>
                            <input class="flip-card__input" name="email" placeholder="Email" type="email" required>
                            <input class="flip-card__input" name="password" placeholder="Password" type="password" required minlength="5">
                            <input class="flip-card__input" name="contact_number" placeholder="Contact Number" type="text" required maxlength="10" minlength="10">
                            <div class="checkbox-wrapper">
                            <input id="terms-checkbox-37" name="checkbox" type="checkbox">
                            <label class="terms-label" for="terms-checkbox-37">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 200 200" class="checkbox-svg" required>
                                <mask fill="white" id="path-1-inside-1_476_5-37">
                                    <rect height="200" width="200"></rect>
                                </mask>
                                <rect mask="url(#path-1-inside-1_476_5-37)" stroke-width="40" class="checkbox-box" height="200" width="200"></rect>
                                <path stroke-width="15" d="M52 111.018L76.9867 136L149 64" class="checkbox-tick"></path>
                                </svg>
                                <span class="label-text">Do you really want to create account </span>
                            </label>
                            </div>
                            
                            <button class="flip-card__btn" name="signup">Confirm!</button>
                        </form>
                    </div>
                </div>
            </label>
        </div>
        
    </div>
    
</body>
</html>