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
                            <h2>Username</h2>
                            <a href="#">Edit profile</a>
                        </div>
                        <div class="profile-meta-line">Location</div>
                        <div class="profile-meta-line">Role</div>


                        <div class="trust-rating">
                            <span class="rating-label">Trust rating</span>
                            <span class="star">◆</span>
                            <span class="star">◆</span>
                            <span class="star">◆</span>
                            <span class="star">◆</span>
                            <span class="star is-empty">◆</span>
                        </div>
                    </div>
                </div>


                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-number">28</div>
                        <div class="stat-label">Completed Trades</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">16</div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number">8</div>
                        <div class="stat-label">Guideful comments</div>
                    </div>
                </div>

  
                <nav class="profile-tabs">
                    <a href="#" class="profile-tab-btn is-active">My notices</a>
                    <a href="#" class="profile-tab-btn">Trust history</a>
                    <a href="#" class="profile-tab-btn">Saved</a>
                </nav>


                <ul class="notice-list">
                    <li class="notice-card">
                        <a class="notice-card-thumb" href="notice.html"></a>
                        <div class="notice-card-body">
                            <div class="notice-card-title-row">
                                <h2><a href="notice.html">Healing herds</a></h2>
                                <span class="badge badge-rarity">Rarity</span>
                            </div>
                            <div class="notice-card-meta">Seattle &nbsp;&middot;&nbsp; 2 hours ago</div>
                            <p class="notice-card-desc">Healing remedy found in Seattle. It's only a few days old. Get it now. Or don't.</p>
                            <div class="notice-card-footer">Expired in 5 days</div>
                        </div>
                    </li>
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
