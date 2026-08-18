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

            <?php include '../Components/sidebar.php'; ?>

    <div class="main-area ">
        <div class="crumb">Admin</div>

        <main class="page-content">


            <div class="admin-header">
                <h1>The All Knowings<br>View</h1>
            </div>


            <div class="admin-stat-grid">
                <div class="admin-stat-box">
                    <div class="num">1,248</div>
                    <div class="lbl">Total Notices:</div>
                </div>
                <div class="admin-stat-box">
                    <div class="num">342</div>
                    <div class="lbl">Active users:</div>
                </div>
                <div class="admin-stat-box">
                    <div class="num">986</div>
                    <div class="lbl">Archived</div>
                </div>
                <div class="admin-stat-box">
                    <div class="num warn">24</div>
                    <div class="lbl">Suspicions</div>
                </div>
            </div>


            <div class="admin-panels">

                <section class="panel">
                    <h2 class="admin-panel-title">Recent reports</h2>


                    <div class="report-item">
                        <div class="avatar-circle sm"></div>
                        <div class="report-text">
                            <div class="report-type">User report: Unsafe</div>
                            <div class="report-user">userName</div>
                        </div>
                        <button class="btn btn-primary btn-sm processing-btn">Review</button>
                    </div>


                    <div class="report-item">
                        <div class="avatar-circle sm"></div>
                        <div class="report-text">
                            <div class="report-type">User report: Unsafe</div>
                            <div class="report-user">userName</div>
                        </div>
                        <button class="btn btn-primary btn-sm processing-btn">Review</button>
                    </div>


                    <div class="report-item">
                        <div class="avatar-circle sm"></div>
                        <div class="report-text">
                            <div class="report-type">User report: Unsafe</div>
                            <div class="report-user">userName</div>
                        </div>
                        <button class="btn btn-primary btn-sm processing-btn">Review</button>
                    </div>


                    <div class="report-item">
                        <div class="avatar-circle sm"></div>
                        <div class="report-text">
                            <div class="report-type">User report: Unsafe</div>
                            <div class="report-user">userName</div>
                        </div>
                        <button class="btn btn-primary btn-sm processing-btn">Review</button>
                    </div>

                    <a class="view-all-link" href="#">View all reports</a>
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


<script>
    document.querySelectorAll('.processing-btn').forEach((btn, index) => {
        btn.addEventListener('click', () => {
            btn.textContent = 'Reviewing...';
            btn.style.opacity = '0.6';
            btn.disabled = true;
            setTimeout(() => {
                alert(`Opening review ticket dashboard for report instance context #${index + 1}`);
                btn.textContent = 'Reviewed';
                btn.style.background = '#2d5a27';
            }, 400);
        });
    });
</script>
</body>
</html>
