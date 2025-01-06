<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - File Backup Management</title>

    <!-- Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- FontAwesome from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white font-sans antialiased flex items-center justify-center min-h-screen">

    <!-- Login Form Container -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8 w-full max-w-md">
        <h1 class="text-3xl font-semibold text-center text-gray-800 dark:text-white mb-6">Login</h1>

        <!-- Login Form -->
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <input type="text" name="username" id="username" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Username" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Password" required>
            </div>

            <button type="submit" class="w-full py-2 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Login
            </button>

            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mt-4"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>

        <!-- Register Link -->
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Don't have an account? <a href="register.php" class="text-blue-500 hover:text-blue-700">Register</a></p>
        </div>

        <!-- Forgot Password Link -->
        <!-- <div class="text-center mt-4">
            <a href="#" class="text-sm text-blue-500 hover:text-blue-700">Forgot password?</a>
        </div> -->
    </div>
    
</body>
</html>
