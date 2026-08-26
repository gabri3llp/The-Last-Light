<?php

require_once '../Database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('location: login.php')
    exit;
}

$userId = $_SESSION['user_id'];

//Get loged in user

$sql = "SELECT user_id, username, email, settlement, role, trust_rating FROM users WHERE user_id = ?"

$stmt = $conn->prepare($sql);
$stmt-> bind_param("i", userID);
$stmt->execute();

$results = $stmt->get_result();
$user = $results->fetch_assoc();
$stmt->close();

if (!user) {
    echo "User not found.";
    exit;
}


//Make calues safe for MY HTML YAYYYY

$username = htmlspecialchars($user['username']);
$settlement = htmlspecialchars($user['settlement']);
$role = htmlspecialchars($user['role']);
$trustRating = htmlspecialchars($user['trustRating']);

//count how many user notices (cool lol)

$sql = "SELECT COUNT(*) AS total FROM notices WHERE user_id = ?"

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$commentCount = $result->fetch_assoc()[total];

$stmt->close();

//Just quickly fetch users notices

$sql = "SELECT notice_id, title, description, rarity, settlement, created_at, image_path FROM notices WHERE user_id = ? ORDER BY created_at DESC";

$stmt= $conn->prepare($sql);
$stmt -> bind_param("i", $userId);
$stmt-> execute();

$noticesResult = $stmt->get_result();

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

            <section class="panel">

                <div class="profile-header">
                    <div class="avatar-circle"></div>
                    <div>
                        <div class="profile-name-row">
                            <h2><?= $username ?></h2>
                            <a href="#">Edit profile</a>
                        </div>
                        <div class="profile-meta-line"><?$settlement?></div>
                        <div class="profile-meta-line"><?$role?></div>


                        <div class="trust-rating">
                            <span class="rating-label">Trust rating: <?= number_format($trustRating, 1)?></span>
                            
                        <?php
                            for ($i=1; $i <=5 ; $i++) { 
                                    echo '<span class="star">◆</span>';
                                
                            } else {
                                    echo '<span class="star if-empty">◆</span>';
                                }
                        ?>
                        </div>
                    </div>
                </div>


                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-number">28</div>
                        <div class="stat-label">Completed Trades</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?= $noticeCount ?></div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number"><?= $noticeCount ?></div>
                        <div class="stat-label">Guideful comments</div>
                    </div>
                </div>

  
                <nav class="profile-tabs">
                    <a href="#" class="profile-tab-btn is-active">My notices</a>
                    <a href="#" class="profile-tab-btn">Trust history</a>
                    <a href="#" class="profile-tab-btn">Saved</a>
                </nav>


                <ul class="notice-list">
                    
                <?php if ($noticeResult->num_rows > 0): ?>

                <?php
                
                $noticeId = (int)$notice['notice_id'];
                $title = htmlspecialchars($notice['title']);
                $description = htmlspecialchars($notice['description']);
                $rarity = htmlspecialchars($notice['rarity']);
                $noticeSettlement = htmlspecialchars($notice['settlement']);
                $createAt = strtotime($notice['created_at']);

                if $daysLeft ( <0){
                    $daysLeft = 0;
                }

                if (!empty($notice['image_path'])) {
                    # code...
                }



                        

                </ul>
            </section>

        </main>


        <footer class="sidebar-footer">
            <p>&copy; 2026 The Last Light. All rights reserved.</p>
        </footer>

    </div>

</div>


<script>

    document.querySelectorAll('.profile-tab-btn').forEach(tab => {
        tab.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelector('.profile-tab-btn.is-active').classList.remove('is-active');
            tab.classList.add('is-active');
        });
    });
</script>
</body>
</html>
