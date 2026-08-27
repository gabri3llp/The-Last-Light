<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../Database/config.php';

$userId = (int)($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

if ($userId <= 0) {
    header('Location: logIn.php');
    exit;
}

$stmt = $conn->prepare("SELECT user_id, username, email, settlement, role, profile_img FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    $conn->close();
    echo "User not found.";
    exit;
}

$username = htmlspecialchars($user['username']);
$settlement = htmlspecialchars($user['settlement'] ?? 'Unassigned Settlement');
$role= htmlspecialchars($user['role'] ?? 'Survivor');
$profileImage = !empty($user['profile_img']) ? htmlspecialchars($user['profile_img']) : 'default_avatar.jpg';

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notices WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$noticeCount = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM comments WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$commentCount = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) AS total FROM notices WHERE accepted_by = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$completedTradesCount = (int)$stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$trustRating= min(5.0, 1.0 + ($commentCount * 0.5));
$formattedTrust = number_format($trustRating, 1);

$stmt = $conn->prepare("UPDATE users SET trust_rating = ? WHERE user_id = ?");
$stmt->bind_param("di", $trustRating, $userId);
$stmt->execute();
$stmt->close();

$starsRow = '';
for ($step = 1; $step <= 5; $step++) {
    $starsRow .= ($step <= round($trustRating)) ? '&#9670;' : '&#9671;';
}


$stmt = $conn->prepare("SELECT notice_id, title, description, rarity, settlement, created_at, image_path FROM notices WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$userNotices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body class="profile-page">
<div class="app-shell">

    <?php include '../Components/sidebar.php'; ?>

    <div class="main-area">
        <div class="crumb">Profile</div>

        <main class="page-content">
            <div class="profile-header">
                <div class="avatar-circle big" style="background-image: url('../Imgs/avatars/<?= $profileImage ?>');"></div>
                
                <div class="profile-user-details">
                    <h2><?= $username ?></h2>
                    <div class="profile-meta-line"><?= $settlement ?></div>
                    <div class="profile-meta-line role-badge"><?= $role ?></div>
                    
                    <form action="../Database/update_profile.php" method="post" enctype="multipart/form-data" class="avatar-form">
                        <input type="file" name="avatar" accept="image/*" required>
                        <button type="submit" class="btn btn-primary btn-sm">Upload Avatar</button>
                    </form>
                </div>
                
                <div class="profile-trust-box">
                    <span class="rating-label">Trust rating: <?= $formattedTrust ?></span>
                    <div class="stars-row"><?= $starsRow ?></div>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-number"><?= $noticeCount ?></div>
                    <div class="stat-label">Posts</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?= $commentCount ?></div>
                    <div class="stat-label">Comments</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?= $completedTradesCount ?></div>
                    <div class="stat-label">Completed Trades</div>
                </div>
            </div>

            <nav class="profile-tabs">
                <a href="#" class="profile-tab-btn is-active">My notices</a>
            </nav>
            
            <ul class="notice-list">
                <?php if (!empty($userNotices)): ?>
                    <?php foreach ($userNotices as $notice): ?>
                        <?php
                            $noticeId    = (int)$notice['notice_id'];
                            $title       = htmlspecialchars($notice['title']);
                            $description = htmlspecialchars($notice['description']);
                        ?>
                        <li class="notice-card">
                            <div class="notice-card-body">
                                <h2><a href="notice.php?id=<?= $noticeId ?>"><?= $title ?></a></h2>
                                <p class="notice-card-desc"><?= $description ?></p>
                                <div class="notice-card-actions-row" style="display: flex; gap: 8px; margin-top: 10px;">
                                    <a href="edit_notice.php?id=<?= $noticeId ?>" class="btn btn-primary-sm" style="text-decoration: none;">Edit</a>
                                    <button type="button" class="btn btn-decline-sm" onclick="document.getElementById('modal-<?= $noticeId ?>').style.display = 'flex'">Delete</button>
                                </div>
                            </div>

                            <div id="modal-<?= $noticeId ?>" class="modal-overlay" style="display: none;">
                                <div class="modal-box">
                                    <h3>Confirm Delete</h3>
                                    <form action="../Database/delete_notice.php" method="post">
                                        <input type="hidden" name="notice_id" value="<?= $noticeId ?>">
                                        <button type="submit" class="btn btn-decline">Delete</button>
                                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-<?= $noticeId ?>').style.display = 'none'">Cancel</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="notice-card"><p>No notices created yet.</p></li>
                <?php endif; ?>
            </ul>
        </main>
    </div>
</div>
</body>
</html>