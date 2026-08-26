<?php
require_once '../Database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$noticeId = (int)($_GET['id'] ?? 0);

if ($noticeId <= 0) {
    header('Location: profile.php');
    exit;
}

$sql = "SELECT notice_id, title, category_id, trade_value, rarity, description, settlement, image_path FROM notices WHERE notice_id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $noticeId, $userId);
$stmt->execute();

$result = $stmt->get_result();
$notice = $result->fetch_assoc();
$stmt->close();

if (!$notice) {
    $conn->close();
    header('Location: profile.php');
    exit;
}

$title = htmlspecialchars($notice['title']);
$categoryId = (int)$notice['category_id'];
$tradeValue = htmlspecialchars($notice['trade_value'] ?? '');
$rarity = htmlspecialchars($notice['rarity']);
$description = htmlspecialchars($notice['description'] ?? '');
$settlement = htmlspecialchars($notice['settlement']);
$imagePath = htmlspecialchars($notice['image_path'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Notice - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body>
<div class="app-shell">

    <?php include '../Components/sidebar.php'; ?>

    <div class="main-area">
        <div class="crumb">Edit Notice</div>

        <main class="page-content">
            <section class="panel">
                <h1 class="panel-title">Edit Notice</h1>

                <form class="create-notice-form" action="/The Last Light/Database/update_notice.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="notice_id" value="<?= $noticeId ?>">

                    <div class="field">
                        <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Title</label>
                        <input type="text" name="title" placeholder="Title" value="<?= $title ?>" required>
                    </div>

                    <div class="field-row">
                        <div class="field">
                            <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Category</label>
                            <select name="category_id" required>
                                <option value="" disabled>Category</option>
                                <option value="1" <?= $categoryId === 1 ? 'selected' : '' ?>>Medicine</option>
                                <option value="2" <?= $categoryId === 2 ? 'selected' : '' ?>>Food</option>
                                <option value="3" <?= $categoryId === 3 ? 'selected' : '' ?>>Weapons</option>
                                <option value="4" <?= $categoryId === 4 ? 'selected' : '' ?>>Shelter</option>
                                <option value="5" <?= $categoryId === 5 ? 'selected' : '' ?>>Materials</option>
                                <option value="6" <?= $categoryId === 6 ? 'selected' : '' ?>>Services</option>
                                <option value="7" <?= $categoryId === 7 ? 'selected' : '' ?>>Quests</option>
                            </select>
                        </div>

                        <div class="field">
                            <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Trade Value</label>
                            <input type="text" name="trade_value" placeholder="Trade Value" value="<?= $tradeValue ?>">
                        </div>
                    </div>

                    <div class="field-row-uneven">
                        <div class="field field-rarity">
                            <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Rarity</label>
                            <select name="rarity" required>
                                <option value="" disabled>Rarity</option>
                                <option value="common" <?= $rarity === 'common' ? 'selected' : '' ?>>Common</option>
                                <option value="uncommon" <?= $rarity === 'uncommon' ? 'selected' : '' ?>>Uncommon</option>
                                <option value="rare" <?= $rarity === 'rare' ? 'selected' : '' ?>>Rare</option>
                                <option value="epic" <?= $rarity === 'epic' ? 'selected' : '' ?>>Epic</option>
                                <option value="legendary" <?= $rarity === 'legendary' ? 'selected' : '' ?>>Legendary</option>
                                <option value="mythic" <?= $rarity === 'mythic' ? 'selected' : '' ?>>Mythic</option>
                            </select>
                        </div>

                        <div class="field field-upload">
                            <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Notice Photo</label>
                            <input type="file" id="noticeImage" name="image" accept="image/jpeg,image/png" class="file-input-hidden">
                            <label for="noticeImage" class="file-label" style="display: block; background: transparent; border: 1px solid var(--ivory-dim, #ccc); color: white; padding: 8px; border-radius: 4px; text-align: center; cursor: pointer;">Choose New Photo</label>
                        </div>
                    </div>

                    <?php if ($imagePath !== ''): ?>
                        <div class="field">
                            <p style="color: var(--ivory-dim); font-size: 0.9rem; margin-top: 4px;">Current file: <?= $imagePath ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="field" style="margin-top: 14px;">
                        <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Description</label>
                        <textarea name="description" placeholder="Description" style="width: 100%; min-height: 100px; background: transparent; border: 1px solid var(--ivory-dim, #ccc); color: white; padding: 8px; border-radius: 4px;"><?= $description ?></textarea>
                    </div>

                    <div class="field">
                        <label style="color: var(--ivory-dim); display: block; margin-bottom: 4px; font-size: 0.85rem;">Settlement Name</label>
                        <input type="text" name="settlement" placeholder="Settlement name" value="<?= $settlement ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="margin-top: 14px;">Update Notice</button>
                </form>
            </section>
        </main>

        <footer class="sidebar-footer">
            <p>&copy; 2026 The Last Light. All rights reserved.</p>
        </footer>
    </div>
</div>
</body>
</html>
<?php
$conn->close();
?>
