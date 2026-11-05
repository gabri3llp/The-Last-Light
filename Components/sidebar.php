
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$userRole = $_SESSION['role'] ?? '';
?>

<aside class="sidebar">
    <div class="sidebar-top">
        <div class="sidebar-menu-icon">
            <span></span><span></span><span></span>
        </div>
        
        <nav class="sidebar-nav">

            <a href="index.php" class="nav-item <?php echo ($current_page == 'index.php') ? 'is-active' : ''; ?>" title="Home">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </a>

            <a href="notice.php" class="nav-item <?php echo ($current_page == 'notice.php') ? 'is-active' : ''; ?>" title="Notices">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" x2="8" y1="13" y2="13"/>
                    <line x1="16" x2="8" y1="17" y2="17"/>
                    <line x1="10" x2="8" y1="9" y2="9"/>
                </svg>
            </a>

            <a href="profile.php" class="nav-item <?php echo ($current_page == 'profile.php') ? 'is-active' : ''; ?>" title="Profile">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </a>

            <?php if ($userRole === 'admin'): ?>
                <a href="admin.php" class="nav-item <?php echo ($current_page == 'admin.php') ? 'is-active' : ''; ?>" title="Admin Dashboard">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L20 6V12C20 17 16.5 20.5 12 22C7.5 20.5 4 17 4 12V6L12 2Z"/>
                        <path d="M9 12L11 14L15 10"/>
                    </svg>
                </a>
            <?php endif; ?>

        </nav>
    </div>

    <div class="sidebar-bottom">
        <a href="logIn.php" class="nav-item logIn-link" title="Logout">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" x2="9" y1="12" y2="12"/>
            </svg>
        </a>
    </div>
</aside>
