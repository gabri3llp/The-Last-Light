<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('location: ../Pages/login.php');
  exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$error = [];

if ($email === '') {
  $error[] = 'Email is required.';
}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $error[] = 'Invalid email format.';
}

if ($password === '') {
  $error[] = 'Password is required.';
}

if(!empty($error)) {
  $errorList = implode('<br>', array_map('htmlspecialchars', $error));
  
  echo <<<HTML
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Log In - The Last Light</title>
        <link rel="stylesheet" href="../Style.css">
    </head>
    <body class="auth-page">
        <div class="auth-card">
            <h2> Could'nt log you in :( </h2>
            <p>{$errorList}</p>
            <a href="../Pages/login.php"> Try again </a>
        </div>
    </body>
    </html>

HTML;
  exit();
}

$sql = "SELECT user_id, username, email, password_hash, role, settlement FROM users WHERE email = ?";

$stmt= $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();

    echo "invalid email or password.";
    exit();
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password_hash'])) {
    $stmt->close();
    $conn->close();

    echo "invalid email or password.";
    exit();
}

$_SESSION['user_id'] = $user['user_id'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];
$_SESSION['settlement'] = $user['settlement'];

$stmt->close();
$conn->close();

header('Location: ../Pages/index.php');
exit;