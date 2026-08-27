<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../Database/config.php';

$noticeId = (int)($_GET['id'] ?? 0);
$userId   = (int)($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

// Current logged-in user profile
$user = [
    'username'     => 'Username',
    'role'         => 'Role',
    'settlement'   => 'Settlement',
    'trust_rating' => 3.0
];

if ($userId > 0) {
    $uStmt = $conn->prepare("SELECT username, role, settlement, trust_rating FROM users WHERE user_id = ?");
    $uStmt->bind_param("i", $userId);
    $uStmt->execute();
    if ($userData = $uStmt->get_result()->fetch_assoc()) {
        $user = $userData;
    }
    $uStmt->close();
}


$stmt = $conn->prepare("SELECT n.*, c.category_name, u.username, u.role, u.trust_rating
                        FROM notices n
                        INNER JOIN categories c ON n.category_id = c.category_id
                        INNER JOIN users u ON n.user_id = u.user_id
                        WHERE n.notice_id = ?");
$stmt->bind_param("i", $noticeId);
$stmt->execute();
$notice = $stmt->get_result()->fetch_assoc();
$stmt->close();


$comments = [];
if ($notice) {
    $cStmt = $conn->prepare("SELECT c.comment, c.created_at, u.username, u.role
                             FROM comments c
                             INNER JOIN users u ON c.user_id = u.user_id
                             WHERE c.notice_id = ?
                             ORDER BY c.created_at DESC");
    $cStmt->bind_param("i", $noticeId);
    $cStmt->execute();
    $comments = $cStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $cStmt->close();
}

$conn->close();

$pageTitle = $notice ? htmlspecialchars($notice['title']) : 'Notice not found';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body>
<div class="app-shell">

    <?php include '../Components/sidebar.php'; ?>

    <div class="main-area">
        <div class="crumb">Product</div>

        <main class="page-content">
            <div class="notice-detail-grid">

                <section class="panel">
                    <h1 class="panel-title">Current Notices</h1>

                    <?php if ($notice): ?>
                        <?php
                            $daysLeft = 7 - floor((time() - strtotime($notice['created_at'])) / 86400);
                            $imgFile  = !empty($notice['image_path']) ? htmlspecialchars($notice['image_path']) : 'placeholder.jpg';
                        ?>
                        <div class="notice-full-card">
                            <div class="notice-full-header">
                                <div>
                                    <h2><?= htmlspecialchars($notice['title']) ?></h2>
                                    <div class="notice-location"><?= htmlspecialchars($notice['settlement']) ?></div>
                                </div>
                                <span class="badge badge-rarity badge-<?= htmlspecialchars($notice['rarity']) ?>">
                                    <?= htmlspecialchars($notice['rarity']) ?>
                                </span>
                            </div>

                            <p class="notice-full-desc"><?= htmlspecialchars($notice['description']) ?></p>

                            <div class="notice-full-image">
                                <img src="../Imgs/notices/<?= $imgFile ?>" alt="<?= htmlspecialchars($notice['title']) ?>">
                            </div>

                            <div class="notice-full-footer">
                                <div class="avatar-circle"></div>
                                <div class="notice-full-poster-info">
                                    <div class="label-sm">Trust: <?= number_format((float)$notice['trust_rating'], 1) ?> &#9670;</div>
                                    <div class="label-sm"><?= htmlspecialchars($notice['role']) ?></div>
                                    <div class="merchant-name"><?= htmlspecialchars($notice['username']) ?></div>
                                </div>
                            </div>

                            <div class="notice-full-actions">
                                <form action="../Database/notice_action.php" method="post">
                                    <input type="hidden" name="notice_id" value="<?= $noticeId ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="btn btn-primary btn-block">Accept</button>
                                </form>
                                
                                <form action="../Database/notice_action.php" method="post">
                                    <input type="hidden" name="notice_id" value="<?= $noticeId ?>">
                                    <input type="hidden" name="action" value="decline">
                                    <button type="submit" class="btn btn-decline btn-block">Decline</button>
                                </form>
                            </div>
                        </div>

                        <p class="notice-card-footer">Expires in <?= $daysLeft ?> days</p>

                        <div id="comments">
                            <hr class="divider-line">
                            <h2 class="create-notice-heading">Comments</h2>

                            <div class="comments-list">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $comment): ?>
                                        <div class="comment-card">
                                            <div class="comment-header">
                                                <strong class="comment-user"><?= htmlspecialchars($comment['username']) ?></strong>
                                                <span class="comment-role"><?= htmlspecialchars($comment['role']) ?></span>
                                            </div>
                                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                                            <small><?= htmlspecialchars($comment['created_at']) ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="no-comments">No comments yet</p>
                                <?php endif; ?>
                            </div>

            <div class="comment-section">
                     <h3 class="comment-title">Leave a comment</h3>
                    <form action="../Database/add_comment.php" method="POST" class="comment-form">
                        <input type="hidden" name="notice_id" value="<?= $noticeId ?>">
                        <textarea name="comment" class="comment-input" rows="4" placeholder="Write your comment here..." required></textarea>
                     <div class="comment-actions">
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </div>
                    </form>
             </div>
                    <?php else: ?>
                        <div class="notice-full-card">
                            <div class="notice-full-header">
                                <div>
                                    <h2>Notice not found</h2>
                                    <div class="notice-location">This sighting may have expired or never existed.</div>
                                </div>
                            </div>
                            <p class="notice-full-desc">
                                <a href="index.php">Back to the notice board</a>
                            </p>
                        </div>
                    <?php endif; ?>
                </section>

                <section class="panel">
                    <h1 class="panel-title">Player Details</h1>

                    <div class="player-details-panel">
                        <div class="avatar-circle"></div>
                        <div class="player-name"><?= htmlspecialchars($user['username']) ?></div>
                    </div>

                    <a class="edit-profile-link" href="profile.php">Edit profile</a>

                    <div class="player-meta"><strong>Trust rating:</strong> <?= number_format((float)$user['trust_rating'], 1) ?> &#9670;</div>
                    <div class="player-meta"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></div>
                    <div class="player-meta"><strong>Settlement:</strong> <?= htmlspecialchars($user['settlement']) ?></div>

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