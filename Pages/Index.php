<?php

require_once '../Database/config.php';

$survivorName = $_SESSION['username'] ?? 'Gabriel';
$safeName     = htmlspecialchars($survivorName);


$sql = "SELECT n.notice_id, n.title, n.description, n.rarity,
               n.settlement, n.created_at, n.image_path,
               c.category_name,
               u.username
        FROM notices n
        INNER JOIN categories c ON n.category_id = c.category_id
        INNER JOIN users u      ON n.user_id      = u.user_id
        WHERE n.status = 'active'
          AND n.created_at > NOW() - INTERVAL 7 DAY
        ORDER BY n.created_at DESC";

$result = $conn->query($sql);

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
                <button class="tab-btn is-active">Nearby</button>
                <button class="tab-btn">All notices</button>
                <button class="tab-btn">My notices</button>
                <button class="tab-btn">Saved</button>
                <button class="tab-filter" title="Filter" aria-label="Filter notices">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16M7 12h10M10 19h4"/></svg>
                </button>
            </nav>


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
                        <p class="notice-card-desc">No active notices right now. The settlement is quiet.</p>
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

</body>
</html>
HTML;

$conn->close();