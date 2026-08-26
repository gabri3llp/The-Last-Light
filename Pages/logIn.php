<?php

$errorBox = '';
if (isset($_GET['error'])) {
    $errorMsg = htmlspecialchars($_GET['error']);
    $errorBox = <<<HTML
        <div style="background: rgba(163, 42, 42, 0.25); border: 1px solid var(--crimson, #a32a2a); color: #ffffff; padding: 12px; border-radius: 4px; margin-bottom: 16px; font-size: 0.9rem; text-align: center; font-weight: bold; font-family: sans-serif;">
            $errorMsg
        </div>
HTML;
}

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In - The Last Light</title>
    <link rel="stylesheet" href="../Style.css">
</head>
<body class="auth-page">

    <div>
        <div class="auth-brand">
            <h1>
                The Last Light
            </h1>
            <img style="width: 50px; height: auto;" src="../Imgs/logo.png" alt="The Last Light Logo" class="auth-logo">
            <p>Look for the light</p>
            
        </div>

        <div class="auth-card">
            $errorBox
            <form action="../Database/login_process.php" method="post">

                <div class="field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="you@settlement.net" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <div class="field-inline-link">
                    <a href="#">Forgot?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Log in</button>
            </form>

            <div class="auth-divider">Or</div>

            <p class="auth-switch">Don't have an account? <a href="signIn.php">Sign Up</a></p>
        </div>
    </div>

</body>
</html>
HTML;
