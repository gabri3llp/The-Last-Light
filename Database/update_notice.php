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
$title = trim($_POST['title'] ?? '');
$categoryId = (int)($_POST['category_id'] ?? 0);
$tradeValue = trim($_POST['trade_value'] ?? '');
$rarity = trim($_POST['rarity'] ?? '');
$description = trim($_POST['description'] ?? '');
$settlement = trim($_POST['settlement'] ?? '');

$error = [];

if ($noticeId <= 0) {
    $error[] = 'Invalid notice.';
}
if ($title === '') {
    $error[] = 'Title is required.';
}
if ($categoryId <= 0) {
    $error[] = 'Category is required.';
}
if ($categoryId < 1 || $categoryId > 7) {
    $error[] = 'Invalid category selected.';
}
if (!in_array($rarity, ['common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic'])) {
    $error[] = 'Invalid rarity selected.';
}
if ($description === '') {
    $error[] = 'Description is required.';
}
if ($settlement === '') {
    $error[] = 'Settlement is required.';
}

if (!empty($error)) {
    $errorList = implode('<br>', array_map('htmlspecialchars', $error));
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h2>Couldn't update notice</h2>
        <p>$errorList</p>
        <a href="../Pages/edit_notice.php?id=$noticeId">Try again</a>
    </div>
</body>
</html>
HTML;
    exit;
}

$sql = "SELECT notice_id, image_path FROM notices WHERE notice_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $noticeId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$notice = $result->fetch_assoc();
$stmt->close();

if (!$notice) {
    $conn->close();
    header('Location: ../Pages/profile.php');
    exit;
}

$imagePath = $notice['image_path'];

if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $conn->close();
        echo "Something went wrong with the image upload.";
        exit;
    }

    $allowedTypes = ['image/jpeg', 'image/png'];
    $imageType = mime_content_type($_FILES['image']['tmp_name']);

    if (!in_array($imageType, $allowedTypes)) {
        $conn->close();
        echo "Only JPG and PNG images are allowed.";
        exit;
    }

    $uploadDirectory = '../Imgs/notices/';
    $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $fileName = uniqid('notice_', true) . '.' . $fileExtension;
    $uploadPath = $uploadDirectory . $fileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
        $conn->close();
        echo "Something went wrong while uploading the image.";
        exit;
    }
    $imagePath = $fileName;
}

$sql = "UPDATE notices SET title = ?, category_id = ?, trade_value = ?, rarity = ?, description = ?, settlement = ?, image_path = ? WHERE notice_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisssssii", $title, $categoryId, $tradeValue, $rarity, $description, $settlement, $imagePath, $noticeId, $userId);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../Pages/notice.php?id=$noticeId");
    exit;
}

$stmt->close();
$conn->close();
echo "Something went wrong. Please try again later.";
exit;
?>
