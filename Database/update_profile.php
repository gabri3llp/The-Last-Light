<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Pages/logIn.php');
    exit;
}

$userId = $_SESSION['user_id'];

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['avatar']['tmp_name'];
    $fileName = $_FILES['avatar']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = 'user_' . $userId . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../Imgs/avatars/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }

        $destPath = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $stmt = $conn->prepare("UPDATE users SET profile_img = ? WHERE user_id = ?");
            $stmt->bind_param("si", $newFileName, $userId);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header('Location: ../Pages/profile.php');
exit;