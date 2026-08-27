<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../Database/config.php';

$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$survivorName  = $_SESSION['username'] ?? 'Survivor';

$selectCat  = (int)($_GET['category_id'] ?? 0);
$selectTier = trim($_GET['rarity'] ?? '');
$view       = $_GET['view'] ?? 'all';


$sql = "SELECT n.notice_id, n.title, n.description, n.rarity,
               n.settlement, n.created_at, n.image_path,
               c.category_name, u.username
        FROM notices n
        INNER JOIN categories c ON n.category_id = c.category_id
        INNER JOIN users u      ON n.user_id      = u.user_id
        WHERE n.status = 'active'
          AND n.created_at > NOW() - INTERVAL 7 DAY
          AND n.notice_id NOT IN (
              SELECT notice_id FROM user_declined_notices WHERE user_id = ?
          )";

$types  = "i";
$params = [$currentUserId];

if ($selectCat > 0) {
    $sql .= " AND n.category_id = ?";
    $types .= "i";
    $params[] = $selectCat;
}

if ($selectTier !== '') {
    $sql .= " AND n.rarity = ?";
    $types .= "s";
    $params[] = $selectTier;
}

if ($view === 'mine') {
    $sql .= " AND n.user_id = ?";
    $types .= "i";
    $params[] = $currentUserId;
} elseif ($view === 'nearby') {
    $sql .= " AND n.settlement = ?";
    $types .= "s";
    $params[] = $_SESSION['settlement'] ?? '';
}

$sql .= " ORDER BY n.created_at DESC";

// 2. Execute safely with prepared statement
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$notices = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body>
<div class="app-shell index">

    <?php include '../Components/sidebar.php'; ?>

    <div class="main-area">
        <div class="crumb">Home</div>

        <main class="page-content">

            <section class="hero-banner">
                <h1>Good Evening <?= htmlspecialchars($survivorName) ?></h1>
                <p class="hero-tagline">Hope. Trade. Survive<br>Look for the light.</p>
            </section>

            <nav class="tab-nav">
                <a href="index.php?view=all" class="tab-btn <?= $view === 'all' ? 'is-active' : '' ?>">All notices</a>
                <a href="index.php?view=nearby" class="tab-btn <?= $view === 'nearby' ? 'is-active' : '' ?>">Nearby</a>
                <a href="index.php?view=mine" class="tab-btn <?= $view === 'mine' ? 'is-active' : '' ?>">My notices</a>
                <button class="tab-btn" type="button">Saved</button>
                <button type="button" class="tab-filter" id="filterToggle" title="Filter" aria-label="Filter notices">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 5h16M7 12h10M10 19h4"/>
                    </svg>
                </button>
            </nav>

            <div id="filterPanel" style="display: none; background: rgba(0,0,0,0.4); padding: 16px; border-radius: 6px; margin-bottom: 20px; border: 1px solid var(--ivory-dim, #555);">
                <form method="get" action="index.php" style="display: flex; flex-direction: column; gap: 12px;">

                    <div class="field">
                        <select name="category_id" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid var(--ivory-dim, #555); background: #1a1a1a; color: #ffffff; font-weight: bold;">
                            <option value="0" style="background: #1a1a1a; color: #ffffff;">All Categories</option>
                            <option value="1" <?= $selectCat === 1 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Medicine</option>
                            <option value="2" <?= $selectCat === 2 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Food</option>
                            <option value="3" <?= $selectCat === 3 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Weapons</option>
                            <option value="4" <?= $selectCat === 4 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Shelter</option>
                            <option value="5" <?= $selectCat === 5 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Materials</option>
                            <option value="6" <?= $selectCat === 6 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Services</option>
                            <option value="7" <?= $selectCat === 7 ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Quests</option>
                        </select>
                    </div>

                    <div class="field">
                        <select name="rarity" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid var(--ivory-dim, #555); background: #1a1a1a; color: #ffffff; font-weight: bold;">
                            <option value="" style="background: #1a1a1a; color: #ffffff;">All Rarities</option>
                            <option value="common" <?= $selectTier === 'common' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Common</option>
                            <option value="uncommon" <?= $selectTier === 'uncommon' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Uncommon</option>
                            <option value="rare" <?= $selectTier === 'rare' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Rare</option>
                            <option value="epic" <?= $selectTier === 'epic' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Epic</option>
                            <option value="legendary" <?= $selectTier === 'legendary' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Legendary</option>
                            <option value="mythic" <?= $selectTier === 'mythic' ? 'selected' : '' ?> style="background: #1a1a1a; color: #ffffff;">Mythic</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 4px;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em;">Apply Filters</button>
                        <a href="index.php" class="btn btn-decline" style="padding: 10px 20px; font-weight: bold; text-decoration: none; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">Clear</a>
                    </div>
                </form>
            </div>

            <ul class="notice-list" id="notice-list-container">
                <?php if (!empty($notices)): ?>
                    <?php foreach ($notices as $notice): ?>
                        <?php
                            $title     = htmlspecialchars($notice['title']);
                            $rarity    = htmlspecialchars($notice['rarity']);
                            $location  = htmlspecialchars($notice['settlement']);
                            $desc      = htmlspecialchars($notice['description']);
                            $posted    = date('M j, g:ia', strtotime($notice['created_at']));
                            $id        = (int)$notice['notice_id'];

                            $daysLeft = 7 - floor((time() - strtotime($notice['created_at'])) / 86400);
                            if ($daysLeft < 0) {
                                $daysLeft = 0;
                            }

                            $imgFile = !empty($notice['image_path']) ? htmlspecialchars($notice['image_path']) : 'placeholder.jpg';
                        ?>
                        <li class="notice-card">
                            <a class="notice-card-thumb" href="notice.php?id=<?= $id ?>">
                                <img src="../Imgs/notices/<?= $imgFile ?>" alt="<?= $title ?>">
                            </a>
                            <div class="notice-card-body">
                                <div class="notice-card-title-row">
                                    <h2><a href="notice.php?id=<?= $id ?>"><?= $title ?></a></h2>
                                    <span class="badge badge-rarity badge-<?= $rarity ?>"><?= $rarity ?></span>
                                </div>
                                <div class="notice-card-meta"><?= $location ?> &nbsp;&middot;&nbsp; <?= $posted ?></div>
                                <p class="notice-card-desc"><?= $desc ?></p>
                                <div class="notice-card-footer">Expires in <?= $daysLeft ?> days</div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="notice-card">
                        <div class="notice-card-body">
                            <p class="notice-card-desc">No active notices match your criteria. The settlement is quiet.</p>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>

        </main>

        <footer class="sidebar-footer">
            <p>&copy; 2026 The Last Light. All rights reserved.</p>
        </footer>

    </div>

</div>

<script>
const filterToggle = document.getElementById('filterToggle');
const filterPanel = document.getElementById('filterPanel');

filterToggle.addEventListener('click', function () {
    if (filterPanel.style.display === 'none') {
        filterPanel.style.display = 'block';
    } else {
        filterPanel.style.display = 'none';
    }
});
</script>

</body>
</html>