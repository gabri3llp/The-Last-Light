<?php

require_once '../Database/config.php';

//make sure my user is logged in

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Pages/login.php");
    exit;
}

// make sure of post req

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../Pages/index.php");
    exit;
}

$noticeId = (int) ($_POST['notice_id'] ?? 0);
$comment = trim($_POST['comment'] ?? '');

if ($noticeId <= 0 || empty($comment)) {
    echo "Invalid notice.";
    exit;
}

if ($comment === '') {
    echo "Comment cannot be empty.";
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "INSERT INTO comments (notice_id, user_id, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("iis", $noticeId, $userId, $comment);

if ($stmt->execute()){

    $stmt->close();
    $conn->close();

    header("Location: ../Pages/notice.php?id=$noticeId#comments");
    exit;

} else {
    echo "something went wrong";

    $stmt->close();
    $conn->close();

    exit;
}


