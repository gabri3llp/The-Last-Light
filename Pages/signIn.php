<?php

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - The Last Light</title>
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
            <form action="../Database/register.php" method="post">

                <div class="field">
                    <label for="survivorName">Survivor name</label>
                    <input type="text" id="survivorName" name="survivorName" placeholder="Your name" required>
                </div>

                <div class="field">
                    <label for="settlementName">Settlement name</label>
                    <input type="text" id="settlementName" name="settlementName" placeholder="Where you're based" required>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="you@settlement.net" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="********" required>
                </div>

                <div class="field">
                    <label>Role</label>
                    <div class="field-row">
                        <div class="field" style="margin-bottom:0;">
                            <input type="radio" id="roleSurvivor" name="role" value="survivor" style="width:auto; margin-right:6px;" checked>
                            <label for="roleSurvivor" style="display:inline; text-transform:none; letter-spacing:normal; font-size:0.9rem; color:var(--ivory);">Survivor</label>
                        </div>
                        <div class="field" style="margin-bottom:0;">
                            <input type="radio" id="roleMerchant" name="role" value="merchant" style="width:auto; margin-right:6px;">
                            <label for="roleMerchant" style="display:inline; text-transform:none; letter-spacing:normal; font-size:0.9rem; color:var(--ivory);">Merchant</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </form>

            <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>

</body>
</html>
HTML;