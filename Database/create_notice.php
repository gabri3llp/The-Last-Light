<?php


require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Pages/index.php');
    exit;
}

$title = $_POST['title'] ?? '';
$categoryId = $_POST['category_id'] ?? '';
$tradeValue = $_POST['trade_value'] ?? '';
$rarity = $_POST['rarity'] ?? '';
$description = $_POST['description'] ?? '';
$settlement = $_POST['settlement'] ?? '';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Pages/login.php');
    exit;
}

$userId= $_SESSION['user_id'];

$error = [];

if ($title === '') {
    $error[] = 'Title is required.';
}
if ($categoryId <= 0) {
    $error[] = 'Category ID is required.';
}

$allowedRarities = ['common', 'uncommon', 'rare', 'epic', 'legendary', 'mythic'];
if (!in_array($rarity, $allowedRarities)) {
    $error[] = 'Invalid rarity value.';
}

if ($description === '') {
    $error[] = 'Description is required.';
}
if ($settlement === '') {
    $error[] = 'Settlement is required.';
}


//optional images
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowExt=['jpg', 'jpeg', 'png']; 
    $ext=strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowExt)) {
        $newFilename = uniqid('notice_', true) . '.' . $ext;
        $targetPath = '../imgs/notices/' . $newFilename;

    if(move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $newFilename;
    }   else {
        $error[] = 'Failed to upload image.';
    } 
} else {
        $error[] = 'Allowed file types: jpg, jpeg, png.';
    }
}


//if vali falues dont try and intert database

if (!empty($error)) {
    $errorList = implode('<br>', array_map('htmlspecialchars', $error));

    echo <<<HTML

<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Create Notice - The Last Light</title>
        <link rel="stylesheet" href="../Css/style.css">
    </head>

    <body class="auth-page">
            <div class="auth-card">
                <h2 style="color: var(--crimson);"> Could not create notice. </h2>
                <p style="color: var(--ivory-dim);">$errorList</p>
                <a class="btn btn-primary btn-block" href="../Pages/createNotice.php">Try Again</a>
            </div>

</body>
</html>
HTML;
    exit();
}




$sql = "INSERT INTO notices (user_Id, category_id, title, description, trade_value, image_path, settlement, rarity, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')";

$stmt = $conn->prepare($sql);
$stmt->bind_param('iissssss', $userId, $categoryId, $title, $description, $tradeValue, $imagePath, $settlement, $rarity);

if ($stmt->execute()) {

    $newId = $stmt->insert_id;
    $stmt->close();
    $conn->close();
    header("Location: ../Pages/notice.php?id=$newId");
    exit();
} else {
    $errorMessage = $stmt->error;
    $stmt->close();
    $conn->close();
    echo "Something went wrong: " . htmlspecialchars($errorMessage);
    exit();
}