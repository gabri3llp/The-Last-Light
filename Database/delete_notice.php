<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Pages/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Pages/profile.php');
    exit;
}

$userId = $_SESSION['user_id'];
$noticeId = (int)($_POST['notice_id'] ?? 0);

if ($noticeId <= 0) {
    header('Location: ../Pages/profile.php');
    exit;
}

$sql = "SELECT notice_id, user_id FROM notices WHERE notice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticeId);
$stmt->execute();
$result = $stmt->get_result();
$notice = $result->fetch_assoc();
$stmt->close();

if (!$notice) {
    $conn->close();
    header('Location: ../Pages/profile.php');
    exit;
}

$userRole = $_SESSION['role'] ?? '';

if ($notice['user_id'] != $userId && $userRole !== 'admin') {
    $conn->close();
    header('Location: ../Pages/index.php');
    exit;
}

$sql = "DELETE FROM comments WHERE notice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticeId);
$stmt->execute();
$stmt->close();

if ($userRole === 'admin') {
    $sql = "DELETE FROM notices WHERE notice_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $noticeId);
} else {
    $sql = "DELETE FROM notices WHERE notice_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $noticeId, $userId);
}
$stmt->execute();
$stmt->close();

$conn->close();

if ($userRole === 'admin') {
    header('Location: ../Pages/admin.php');
} else {
    header('Location: ../Pages/profile.php');
}
exit;
?>
