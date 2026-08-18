<?php

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Pages/signin.php');
    exit;
}

$username = trim($_POST['survivorName'] ?? '');
$password = trim($_POST['password'] ?? '');
$email = trim($_POST['email'] ?? '');
$settlement = trim($_POST['settlementName'] ?? '');
$role = trim($_POST['role'] ?? '');

$error = [];

if ($username === '') {
    $error[] = 'Username is required.';
}

if ($settlement === "") {
    $error[] = 'Settlement is required.';
}

if ($email === '') {
    $error[] = 'Email is required.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = 'Invalid email format.';
}

if ($password === '') {
    $error[] = 'Password is required.';
}

if (strlen($password) < 6) {
    $error[] = 'Password must be at least 6 characters long.';
}

if (!in_array($role, ['survivor', 'merchant'])) {
    $error[] = 'Invalid role selected.';
}


if (empty($error)) {
    $sql = "SELECT user_id FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error[] = 'Email is already registered.';
    }

    $stmt->close();

}

if (empty($error)) {
    $sql = "SELECT user_id FROM users WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error[] = 'Survivor name is already taken.';
    } 
        $stmt->close();
    }

    if (!empty($error)) {
        $errorList = implode('<br>', array_map('htmlspecialchars', $error));

        echo <<<HTML

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Error The last light </title>
        </head>
        <body class="auth-page">

        <div>
            <H2> Couldn't create an account </H2>
        </div>

        <p>$errorList</p>

        <a href="../Pages/signIn.php"> Try again. Go back to Sign In </a>

        </body>
        </html>
        HTML;

            exit;
    }

    // Inserting my users

    $sql = "INSERT INTO users (username, email, password_hash, settlement, role) VALUES (?, ?, ?, ?, ?)";

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $username, $email, $passwordHash, $settlement, $role);

    if ($stmt->execute()) {

        $newUserId = $stmt->insert_id;
        
        // Registration successful

        $_SESSION['user_id'] = $newUserId;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['settlement'] = $settlement;

        $stmt->close();
        $conn->close();

        header('Location: ../Pages/index.php');
        exit;

    } else {
        echo "something went wrong. Please try again later.";

        $stmt->close();
        $conn->close();

        exit;
    }



