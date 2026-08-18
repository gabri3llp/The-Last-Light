<?php

require_once '../Database/config.php';

$noticeId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT n.notice_id, n.title, n.description, n.rarity,
               n.settlement, n.created_at, n.image_path,
               c.category_name,
               u.username, u.role, u.trust_rating
        FROM notices n
        INNER JOIN categories c ON n.category_id = c.category_id
        INNER JOIN users u      ON n.user_id      = u.user_id
        WHERE n.notice_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $noticeId);
$stmt->execute();
$result = $stmt->get_result();
$notice = $result->fetch_assoc();


$viewerName       = $_SESSION['username']     ?? 'Username';
$viewerRole       = $_SESSION['role']         ?? 'Role';
$viewerSettlement = $_SESSION['settlement']   ?? 'Settlement';
$viewerTrust      = $_SESSION['trust_rating'] ?? '3.0';

$safeViewerName  = htmlspecialchars($viewerName);
$safeViewerRole  = htmlspecialchars($viewerRole);
$safeViewerSett  = htmlspecialchars($viewerSettlement);
$safeViewerTrust = htmlspecialchars($viewerTrust);

$pageTitle = $notice ? htmlspecialchars($notice['title']) : 'Notice not found';

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$pageTitle - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body>
<div class="app-shell">

HTML;

include '../Components/sidebar.php';

echo <<<HTML

    <div class="main-area">
        <div class="crumb">Product</div>

        <main class="page-content">
            <div class="notice-detail-grid">

                <section class="panel">
                    <h1 class="panel-title">Current Notices</h1>

HTML;

if ($notice) {
    $title    = htmlspecialchars($notice['title']);
    $rarity   = htmlspecialchars($notice['rarity']);
    $location = htmlspecialchars($notice['settlement']);
    $desc     = htmlspecialchars($notice['description']);
    $poster   = htmlspecialchars($notice['username']);
    $role     = htmlspecialchars($notice['role']);
    $trust    = htmlspecialchars($notice['trust_rating']);

    $createdTs = strtotime($notice['created_at']);
    $daysLeft  = 7 - floor((time() - $createdTs) / 86400);

    $imgFile = !empty($notice['image_path'])
        ? htmlspecialchars($notice['image_path'])
        : 'placeholder.jpg';
    $imgTag = "<img src=\"../Imgs/notices/$imgFile\" alt=\"$title\">";

    echo <<<HTML
                    <div class="notice-full-card">
                        <div class="notice-full-header">
                            <div>
                                <h2>$title</h2>
                                <div class="notice-location">$location</div>
                            </div>
                            <span class="badge badge-rarity badge-$rarity">$rarity</span>
                        </div>

                        <p class="notice-full-desc">$desc</p>

                        <div class="notice-full-image">$imgTag</div>

                        <div class="notice-full-footer">
                            <div class="avatar-circle"></div>
                            <div class="notice-full-poster-info">
                                <div class="label-sm">Trust: $trust &#9670;</div>
                                <div class="label-sm">$role</div>
                                <div class="merchant-name">$poster</div>
                            </div>
                        </div>

                        <div class="notice-full-actions">
                            <button class="btn btn-primary btn-block" id="btn-accept">Accept</button>
                            <button class="btn btn-decline btn-block" id="btn-decline">Decline</button>
                        </div>

                        <a class="view-comments-link" href="#comments">View comments</a>
                    </div>

                    <p class="notice-card-footer" style="text-align:center; margin-top:14px;">Expires in $daysLeft days</p>
HTML;
} else {
    echo <<<HTML
                    <div class="notice-full-card">
                        <div class="notice-full-header">
                            <div>
                                <h2>Notice not found</h2>
                                <div class="notice-location">This sighting may have expired or never existed.</div>
                            </div>
                        </div>
                        <p class="notice-full-desc">
                            <a href="index.php" style="color: var(--moss);">Back to the notice board</a>
                        </p>
                    </div>
HTML;
}

echo <<<HTML
                </section>

                <section class="panel">
                    <h1 class="panel-title">Player Details</h1>

                    <div class="player-details-panel">
                        <div class="avatar-circle"></div>
                        <div class="player-name">$safeViewerName</div>
                    </div>

                    <a class="edit-profile-link" href="profile.php">Edit profile</a>

                    <div class="player-meta"><strong>Trust rating:</strong> $safeViewerTrust &#9670;</div>
                    <div class="player-meta"><strong>Role:</strong> $safeViewerRole</div>
                    <div class="player-meta"><strong>Settlement:</strong> $safeViewerSett</div>

                    <hr class="divider-line">

                    <h2 class="create-notice-heading">Create Notice</h2>

                    <form class="create-notice-form" action="../Database/create_notice.php" method="post" enctype="multipart/form-data">
                        <div class="field">
                            <input type="text" name="title" placeholder="Title" required>
                        </div>
                        <div class="field-row">
                            <div class="field">
                                <select name="category_id" required>
                                    <option value="" disabled selected>Category</option>
                                    <option value="1">Medicine</option>
                                    <option value="2">Food</option>
                                    <option value="3">Weapons</option>
                                    <option value="4">Shelter</option>
                                    <option value="5">Materials</option>
                                    <option value="6">Services</option>
                                    <option value="7">Quests</option>
                                </select>
                            </div>
                            <div class="field">
                                <input type="text" name="trade_value" placeholder="Trade Value">
                            </div>
                        </div>
                        <div class="field-row-uneven">
    <div class="field field-rarity">
        <select name="rarity" required>
            <option value="" disabled selected>Rarity</option>
            <option value="common">Common</option>
            <option value="uncommon">Uncommon</option>
            <option value="rare">Rare</option>
            <option value="epic">Epic</option>
            <option value="legendary">Legendary</option>
            <option value="mythic">Mythic</option>
        </select>
    </div>
<div class="field field-upload">
        <input type="file" id="noticeImage" name="image" accept="image/jpeg,image/png" class="file-input-hidden">
        <label for="noticeImage" class="file-label">Choose Photo</label>
    </div>
</div>
                        <div class="field">
                            <input type="text" name="description" placeholder="Description">
                        </div>
                        <div class="field">
                            <input type="text" name="settlement" placeholder="Settlement name">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Create Notice</button>
                    </form>
                </section>

            </div>
        </main>

        <footer class="sidebar-footer">
            <p>&copy; 2026 The Last Light. All rights reserved.</p>
        </footer>

    </div>

</div>

</body>
</html>
HTML;

$stmt->close();
$conn->close();