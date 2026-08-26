<?php
require_once '../Database/config.php';

$survivorName = $_SESSION['username'] ?? 'Gabriel';
$safeName     = htmlspecialchars($survivorName);

$select_cat  = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$select_tier = isset($_GET['rarity']) ? trim($_GET['rarity']) : '';

$view = $_GET['view'] ?? 'all';

$nearbyActive = $view === 'nearby' ? 'is-active' : '';
$allActive    = $view === 'all'    ? 'is-active' : '';
$mineActive   = $view === 'mine'   ? 'is-active' : '';

$sql = "SELECT n.notice_id, n.title, n.description, n.rarity,
               n.settlement, n.created_at, n.image_path,
               c.category_name,
               u.username
        FROM notices n
        INNER JOIN categories c ON n.category_id = c.category_id
        INNER JOIN users u      ON n.user_id      = u.user_id
        WHERE n.status = 'active'
        AND n.created_at > NOW() - INTERVAL 7 DAY";

if ($select_cat > 0) {
    $sql .= " AND n.category_id = $select_cat";
}

if ($select_tier !== '') {
    $clean_tier = $conn->real_escape_string($select_tier);
    $sql .= " AND n.rarity = '$clean_tier'";
}

if ($view === 'mine') {
    $current_userId = (int)($_SESSION['user_id'] ?? 0);
    $sql .= " AND n.user_id = $current_userId";
} elseif ($view === 'nearby') {
    $current_settlement = $_SESSION['settlement'] ?? '';
    $clean_settlement = $conn->real_escape_string($current_settlement);
    $sql .= " AND n.settlement = '$clean_settlement'";
}

$sql .= " ORDER BY n.created_at DESC";
$result = $conn->query($sql);

$cat1 = $select_cat === 1 ? 'selected' : '';
$cat2 = $select_cat === 2 ? 'selected' : '';
$cat3 = $select_cat === 3 ? 'selected' : '';
$cat4 = $select_cat === 4 ? 'selected' : '';
$cat5 = $select_cat === 5 ? 'selected' : '';
$cat6 = $select_cat === 6 ? 'selected' : '';
$cat7 = $select_cat === 7 ? 'selected' : '';

$tier_common    = $select_tier === 'common' ? 'selected' : '';
$tier_uncommon  = $select_tier === 'uncommon' ? 'selected' : '';
$tier_rare      = $select_tier === 'rare' ? 'selected' : '';
$tier_epic      = $select_tier === 'epic' ? 'selected' : '';
$tier_legendary = $select_tier === 'legendary' ? 'selected' : '';
$tier_mythic    = $select_tier === 'mythic' ? 'selected' : '';

echo <<<HTML
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
HTML;

include '../Components/sidebar.php';

echo <<<HTML
    <div class="main-area">
        <div class="crumb">Home</div>

        <main class="page-content">

            <section class="hero-banner">
                <h1>Good Evening $safeName</h1>
                <p class="hero-tagline">Hope. Trade. Survive<br>Look for the light.</p>
            </section>

            <nav class="tab-nav">
                <a href="index.php?view=all" class="tab-btn $allActive">All notices</a>
                <a href="index.php?view=nearby" class="tab-btn $nearbyActive">Nearby</a>
                <a href="index.php?view=mine" class="tab-btn $mineActive">My notices</a>
                <button class="tab-btn">Saved</button>
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
                            <option value="1" $cat1 style="background: #1a1a1a; color: #ffffff;">Medicine</option>
                            <option value="2" $cat2 style="background: #1a1a1a; color: #ffffff;">Food</option>
                            <option value="3" $cat3 style="background: #1a1a1a; color: #ffffff;">Weapons</option>
                            <option value="4" $cat4 style="background: #1a1a1a; color: #ffffff;">Shelter</option>
                            <option value="5" $cat5 style="background: #1a1a1a; color: #ffffff;">Materials</option>
                            <option value="6" $cat6 style="background: #1a1a1a; color: #ffffff;">Services</option>
                            <option value="7" $cat7 style="background: #1a1a1a; color: #ffffff;">Quests</option>
                        </select>
                    </div>

                    <div class="field">
                        <select name="rarity" style="width: 100%; padding: 10px; border-radius: 4px; border: 1px solid var(--ivory-dim, #555); background: #1a1a1a; color: #ffffff; font-weight: bold;">
                            <option value="" style="background: #1a1a1a; color: #ffffff;">All Rarities</option>
                            <option value="common" $tier_common style="background: #1a1a1a; color: #ffffff;">Common</option>
                            <option value="uncommon" $tier_uncommon style="background: #1a1a1a; color: #ffffff;">Uncommon</option>
                            <option value="rare" $tier_rare style="background: #1a1a1a; color: #ffffff;">Rare</option>
                            <option value="epic" $tier_epic style="background: #1a1a1a; color: #ffffff;">Epic</option>
                            <option value="legendary" $tier_legendary style="background: #1a1a1a; color: #ffffff;">Legendary</option>
                            <option value="mythic" $tier_mythic style="background: #1a1a1a; color: #ffffff;">Mythic</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px; margin-top: 4px;">
                        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em;">Apply Filters</button>
                        <a href="index.php" class="btn btn-decline" style="padding: 10px 20px; font-weight: bold; text-decoration: none; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">Clear</a>
                    </div>
                </form>
            </div>

            <ul class="notice-list" id="notice-list-container">
HTML;

if ($result && $result->num_rows > 0) {
    while ($notice = $result->fetch_assoc()) {
        $title    = htmlspecialchars($notice['title']);
        $rarity   = htmlspecialchars($notice['rarity']);
        $location = htmlspecialchars($notice['settlement']);
        $desc     = htmlspecialchars($notice['description']);
        $posted   = date('M j, g:ia', strtotime($notice['created_at']));
        $id       = (int) $notice['notice_id'];

        $createdTs = strtotime($notice['created_at']);
        $daysLeft  = 7 - floor((time() - $createdTs) / 86400);

        if ($daysLeft < 0) {
            $daysLeft = 0;
        }

        $imgFile = !empty($notice['image_path'])
            ? htmlspecialchars($notice['image_path'])
            : 'placeholder.jpg';
        $imgTag = "<img src=\"../Imgs/notices/$imgFile\" alt=\"$title\">";

        echo <<<HTML
                <li class="notice-card">
                    <a class="notice-card-thumb" href="notice.php?id=$id">$imgTag</a>
                    <div class="notice-card-body">
                        <div class="notice-card-title-row">
                            <h2><a href="notice.php?id=$id">$title</a></h2>
                            <span class="badge badge-rarity badge-$rarity">$rarity</span>
                        </div>
                        <div class="notice-card-meta">$location &nbsp;&middot;&nbsp; $posted</div>
                        <p class="notice-card-desc">$desc</p>
                        <div class="notice-card-footer">Expires in $daysLeft days</div>
                    </div>
                </li>
HTML;
    }
} else {
    echo <<<HTML
                <li class="notice-card">
                    <div class="notice-card-body">
                        <p class="notice-card-desc">No active notices match your criteria. The settlement is quiet.</p>
                    </div>
                </li>
HTML;
}

echo <<<HTML
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
HTML;

$conn->close();
?>
