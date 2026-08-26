<?php
require_once '../Database/config.php';

$userId = $_SESSION['user_id'] ?? 0;
$userRole = $_SESSION['role'] ?? '';

if ($userId <= 0) {
    header('Location: login.php');
    exit;
}

if ($userRole !== 'admin') {
    header('Location: index.php');
    exit;
}

$noticesCount = 0;
$usersCount = 0;
$commentsCount = 0;

$noticesResult = $conn->query("SELECT COUNT(*) AS total FROM notices");
if ($noticesResult) {
    $noticesRow = $noticesResult->fetch_assoc();
    $noticesCount = (int)$noticesRow['total'];
}

$usersResult = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($usersResult) {
    $usersRow = $usersResult->fetch_assoc();
    $usersCount = (int)$usersRow['total'];
}

$commentsResult = $conn->query("SELECT COUNT(*) AS total FROM comments");
if ($commentsResult) {
    $commentsRow = $commentsResult->fetch_assoc();
    $commentsCount = (int)$commentsRow['total'];
}

$adminNoticesResult = $conn->query(
                    "SELECT n.notice_id, n.title, n.settlement, n.rarity, u.username, c.category_name
                    FROM notices n
                    INNER JOIN users u ON n.user_id = u.user_id
                    INNER JOIN categories c ON n.category_id = c.category_id
                    ORDER BY n.created_at DESC LIMIT 4");

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body class="admin-page">
<div class="app-shell">
HTML;

include '../Components/sidebar.php';

echo <<<HTML
    <div class="main-area ">
        <div class="crumb">Admin</div>

        <main class="page-content">

            <div class="admin-header">
                <h1>The All Knowings<br>View</h1>
            </div>

            <div class="admin-stat-grid" style="display: grid; grid-template-columns: repeat(3, 11fr); gap: 16px;">
                <div class="admin-stat-box">
                    <div class="num">$noticesCount</div>
                    <div class="lbl">Total Notices:</div>
                </div>
                <div class="admin-stat-box">
                    <div class="num">$usersCount</div>
                    <div class="lbl">Active users:</div>
                </div>
                <div class="admin-stat-box">
                    <div class="num">$commentsCount</div>
                    <div class="lbl">Archived</div>
                </div>
            </div>

            <div class="admin-panels">

                <section class="panel">
                    <h2 class="admin-panel-title">Current Notices</h2>
HTML;

if ($adminNoticesResult && $adminNoticesResult->num_rows > 0) {
    while ($notice = $adminNoticesResult->fetch_assoc()) {
        $noticeId = (int)$notice['notice_id'];
        $title = htmlspecialchars($notice['title']);
        $username = htmlspecialchars($notice['username']);
        $settlement = htmlspecialchars($notice['settlement']);
        $rarity = htmlspecialchars($notice['rarity']);

        echo <<<HTML
                    <div class="report-item" style="display: flex; align-items: center; justify-content: space-between; padding: 12px; margin-bottom: 10px; background: rgba(0,0,0,0.15); border-radius: 4px; border-left: 3px solid var(--ivory-dim, #555);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="avatar-circle sm"></div>
                            <div class="report-text">
                                <div class="report-type" style="font-weight: bold; color: white;"><a href="notice.php?id=$noticeId" style="color: white; text-decoration: none;">$title</a> <span class="badge badge-rarity badge-$rarity" style="font-size: 0.65rem; padding: 2px 6px; margin-left: 4px;">$rarity</span></div>
                                <div class="report-user" style="font-size: 0.85rem; color: var(--ivory-dim);">$username &middot; $settlement</div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-decline-sm" style="background: var(--crimson, #a32a2a); color: white; border: none; padding: 6px 14px; font-size: 0.8rem; font-weight: bold; border-radius: 4px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em;" onclick="document.getElementById('admin-modal-$noticeId').style.display = 'flex'">Delete</button>
                    </div>

                    <div id="admin-modal-$noticeId" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.75); align-items: center; justify-content: center;">
                        <div style="background: #1e1e1e; border: 2px solid var(--crimson, #a32a2a); padding: 24px; border-radius: 6px; max-width: 400px; width: 90%; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
                            <h3 style="color: #ffffff; margin-top: 0; text-transform: uppercase; letter-spacing: 0.05em; font-family: sans-serif;">Confirm Action</h3>
                            <p style="color: var(--ivory-dim, #ccc); font-size: 0.95rem; margin: 16px 0 24px 0; font-family: sans-serif;">Are you sure you want to permanently delete this notice sighting from the board?</p>
                            <div style="display: flex; justify-content: center; gap: 12px;">
                                <form action="../Database/delete_notice.php" method="post" style="margin: 0; display: inline;">
                                    <input type="hidden" name="notice_id" value="$noticeId">
                                    <button type="submit" style="background: var(--crimson, #a32a2a); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Confirm Delete</button>
                                </form>
                                <button type="button" onclick="document.getElementById('admin-modal-$noticeId').style.display = 'none'" style="background: transparent; border: 1px solid var(--ivory-dim, #ccc); color: white; padding: 8px 16px; border-radius: 4px; font-weight: bold; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;">Cancel</button>
                            </div>
                        </div>
                    </div>
HTML;
    }
} else {
    echo <<<HTML
                    <div class="report-item">
                        <div class="report-text">
                            <div class="report-type" style="color: var(--ivory-dim);">No notice board listings found.</div>
                        </div>
                    </div>
HTML;
}

echo <<<HTML
                    <a class="view-all-link" href="index.php">View all notices</a>
                </section>

                <section class="panel">
                    <h2 class="admin-panel-title">System log</h2>

                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>

                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>

                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>
   
                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>
                 
                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>

                    <div class="log-item">
                        <span class="log-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h10"/></svg>
                        </span>
                        <span class="log-text">Notice achieved by The All Knowing</span>
                        <span class="log-time">2 minutes ago</span>
                    </div>
                </section>

            </div>

            <p class="admin-tagline">He knows all and the system endures.</p>

        </main>
    </div>
</div>
</body>
</html>
HTML;

$conn->close();
?>
