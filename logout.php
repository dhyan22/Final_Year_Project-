<?php
session_start();
session_unset(); 
session_destroy(); 

header("Cache-Control: no-cache, must-revalidate, no-store, max-age=0, post-check=0, pre-check=0");
header("Pragma: no-cache");
header("Expires: 0");

header("Location: login_signup.php"); 
exit();
?>
