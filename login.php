<?php
session_start();

// If already logged in, redirect to admin
if (isset($_SESSION['zenobia_admin']) && $_SESSION['zenobia_admin'] === true) {
    header('Location: admin.php');
    exit();
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    // Simple single-password auth – change this before going live
    if ($password === 'zenobia-admin') {
        $_SESSION['zenobia_admin'] = true;
        header('Location: admin.php');
        exit();
    } else {
        $error_message = 'Invalid password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zenobia Admin Login</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>
<body>
    <div class="admin-shell">
        <main class="admin-main">
            <section class="card">
                <h1 style="text-align:center; letter-spacing:0.2em; text-transform:uppercase;">Zenobia Admin</h1>
                <p style="text-align:center; margin-bottom:1rem;">Login to manage site content</p>

                <?php if ($error_message): ?>
                    <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <form method="POST" action="">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>

                    <button type="submit">Login</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>

