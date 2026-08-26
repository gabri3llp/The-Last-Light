<?php
require_once '../Database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT user_id, username, email, settlement, role, trust_rating FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit;
}

$username = htmlspecialchars($user['username']);
$settlement = htmlspecialchars($user['settlement']);
$role = htmlspecialchars($user['role']);
$trustRating = (float)$user['trust_rating'];

$sql = "SELECT COUNT(*) AS total FROM notices WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$noticeCount = $result->fetch_assoc()['total'];
$stmt->close();

$sql = "SELECT COUNT(*) AS total FROM comments WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$commentCount = $result->fetch_assoc()['total'];
$stmt->close();

$sql = "SELECT notice_id, title, description, rarity, settlement, created_at, image_path FROM notices WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$noticesResult = $stmt->get_result();

// Safe evaluation code translating float entries into HTML entity hex blocks
$formattedTrust = number_format($trustRating, 1);
$starsRow = '';
for ($step = 1; $step <= 5; $step++) {
    if ($step <= round($trustRating)) {
        // Safe hex entity string code for a solid, filled diamond shape: ◆
        $starsRow .= '&#9670;';
    } else {
        // Safe hex entity string code for an open, empty diamond shape: ◇
        $starsRow .= '&#9671;';
    }
}

echo <<<HTML
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
HTML;

include '../Components/sidebar.php';

echo <<<HTML
    <div class="main-area">
    <div class="crumb">Profile</div>

    <main class="page-content">
        <!-- Master Header Flex Container -->
        <div class="profile-header" style="display: flex; align-items: center; gap: 24px; margin-bottom: 32px;">
            <div class="avatar-circle big"></div>
            
            <!-- User Text Profile Box (Separated from summary cards) -->
            <div class="profile-user-details" style="flex: 1;">
                <h2 style="margin: 0 0 4px 0; color: white; font-size: 1.8rem;">$username</h2>
                <div class="profile-meta-line" style="color: var(--ivory-dim); font-size: 0.95rem; margin-bottom: 2px;">$settlement</div>
                <div class="profile-meta-line" style="color: var(--ivory-dim); font-size: 0.95rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em;">$role</div>
            </div>
            
            <!-- Trust Rating Display Box -->
            <div class="profile-trust-box" style="text-align: right;">
                <span class="rating-label" style="display: block; color: var(--ivory-dim); margin-bottom: 4px; font-size: 0.9rem;">Trust rating: $formattedTrust</span>
                <div class="stars-row" style="color: var(--crimson, #a32a2a); font-size: 1.2rem; letter-spacing: 2px;">$starsRow</div>
            </div>
        </div>

        <!-- Metric Grid Rows (3 Columns Match) -->
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-number">$noticeCount</div>
                <div class="stat-label">Posts</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">$commentCount</div>
                <div class="stat-label">Comments</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">0</div>
                <div class="stat-label">Completed Trades</div>
            </div>
        </div>

        <!-- Navigation Tab Button Row Links -->
        <nav class="profile-tabs" style="display: flex; gap: 16px; margin-top: 32px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 8px;">
            <a href="#" class="profile-tab-btn is-active" style="text-decoration: none; padding: 6px 12px; color: white;">My notices</a>
            <a href="#" class="profile-tab-btn" style="text-decoration: none; padding: 6px 12px; color: var(--ivory-dim);">Trust history</a>
            <a href="#" class="profile-tab-btn" style="text-decoration: none; padding: 6px 12px; color: var(--ivory-dim);">Saved</a>
        </nav>
            <ul class="notice-list">
HTML;

if ($noticesResult->num_rows > 0) {
    while ($notice = $noticesResult->fetch_assoc()) {
        $noticeId = (int)$notice['notice_id'];
        $title = htmlspecialchars($notice['title']);
        $description = htmlspecialchars($notice['description']);
        $rarity = htmlspecialchars($notice['rarity']);
        $noticeSettlement = htmlspecialchars($notice['settlement']);
        $createdAt = strtotime($notice['created_at']);

        $daysLeft = 7 - floor((time() - $createdAt) / 86400);
        if ($daysLeft < 0) {
            $daysLeft = 0;
        }

        $image = !empty($notice['image_path']) ? htmlspecialchars($notice['image_path']) : 'placeholder.jpg';

        echo <<<HTML
                <li class="notice-card">
                    <a class="notice-card-thumb" href="notice.php?id=$noticeId" style="background-image: url('../Imgs/notices/$image');"></a>
                    <div class="notice-card-body">
                        <div class="notice-card-title-row">
                            <h2><a href="notice.php?id=$noticeId">$title</a></h2>
                            <span class="badge badge-rarity badge-$rarity">$rarity</span>
                        </div>
                        <div class="notice-card-meta">$noticeSettlement</div>
                        <p class="notice-card-desc">$description</p>
                        <div class="notice-card-footer">Expires in $daysLeft days</div>

                        <div class="notice-card-actions-row" style="display: flex; gap: 10px; margin-top: 14px; align-items: center;">
                            <a href="edit_notice.php?id=$noticeId" class="btn btn-secondary-sm" style="display: inline-flex; align-items: center; background: transparent; border: 1px solid var(--ivory-dim, #ccc); color: var(--ivory, #fff); padding: 5px 12px; font-size: 0.8rem; font-weight: bold; border-radius: 4px; text-decoration: none; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s;">Edit</a>
                            
                            <!-- Trigger button targets an explicitly scoped inline modal -->
                            <button type="button" class="btn btn-decline-sm" style="background: var(--crimson, #a32a2a); color: #ffffff; border: none; padding: 6px 12px; font-size: 0.8rem; font-weight: bold; border-radius: 4px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em;" onclick="document.getElementById('modal-$noticeId').style.display = 'flex'">Delete</button>
                        </div>
                    </div>

                    <!-- Clean inline modal layout avoiding global JS listeners -->
                    <div id="modal-$noticeId" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.75); align-items: center; justify-content: center;">
                        <div style="background: #1e1e1e; border: 2px solid var(--crimson, #a32a2a); padding: 24px; border-radius: 6px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                            <h3 style="color: #ffffff; margin-top: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: sans-serif;">Confirm Action</h3>
                            <p style="color: var(--ivory-dim, #ccc); font-size: 0.95rem; margin: 16px 0 24px 0; font-family: sans-serif;">Are you sure you want to permanently delete this notice sighting from the board?</p>
                            <div style="display: flex; justify-content: center; gap: 12px;">
                                <form action="../Database/delete_notice.php" method="post" style="margin: 0; display: inline;">
                                    <input type="hidden" name="notice_id" value="$noticeId">
                                    <button type="submit" style="background: var(--crimson, #a32a2a); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Confirm Delete</button>
                                </form>
                                <button type="button" onclick="document.getElementById('modal-$noticeId').style.display = 'none'" style="background: transparent; border: 1px solid var(--ivory-dim, #ccc); color: white; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Cancel</button>
                            </div>
                        </div>
                    </div>
                </li>
HTML;
    }
} else {
    echo <<<HTML
                <li class="notice-card">
                    <div class="notice-card-body">
                        <h2>No notices yet</h2>
                        <p class="notice-card-desc">You haven't created any notices.</p>
                    </div>
                </li>
HTML;
}

echo <<<HTML
            </ul>
        </main>
    </div>
</div>
</body>
</html>
HTML;

$conn->close();
?>
