<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Pages/logIn.php');
    exit;
}

$userId   = (int)$_SESSION['user_id'];
$noticeId = isset($_POST['notice_id']) ? (int)$_POST['notice_id'] : 0;



$action   = $_POST['action'] ?? '';

if ($noticeId > 0 && in_array($action, ['accept', 'decline'])) {
    if ($action === 'decline') {
        $stmt = $conn->prepare("INSERT IGNORE INTO user_declined_notices (user_id, notice_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $userId, $noticeId);
        $stmt->execute();
        $stmt->close();
        
        header('Location: ../Pages/Index.php');
        exit;
    } elseif ($action === 'accept') {
        $stmt = $conn->prepare("UPDATE notices SET status = 'completed', accepted_by = ? WHERE notice_id = ?");
        $stmt->bind_param("ii", $userId, $noticeId);
        $stmt->execute();
        $stmt->close();

        header('Location: ../Pages/profile.php');
        exit;
    }
}

header('Location: ../Pages/Index.php');
exit;