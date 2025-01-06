<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password === $confirm_password) {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "Username already exists!";
        } else {
            // Hash the password and insert into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->execute([$username, $hashed_password]);
            $success = "Registration successful! You can now log in.";
        }
    } else {
        $error = "Passwords do not match!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - File Backup Management</title>

    <!-- Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- FontAwesome from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white font-sans antialiased flex items-center justify-center min-h-screen">

    <!-- Register Form Container -->
    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-lg p-8 w-full max-w-md">
        <h1 class="text-3xl font-semibold text-center text-gray-800 dark:text-white mb-6">Create an Account</h1>

        <!-- Register Form -->
        <form method="POST">
            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <input type="text" name="username" id="username" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Username" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Password" required>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Confirm Password" required>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                Register
            </button>

            <?php if (isset($error)): ?>
                <p class="text-red-500 text-center mt-4"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="text-green-500 text-center mt-4"><?php echo $success; ?></p>
            <?php endif; ?>
        </form>

        <!-- Link to Login -->
        <div class="text-center mt-4">
            <p class="text-sm text-gray-600 dark:text-gray-400">Already have an account? <a href="index.php" class="text-blue-500 hover:text-blue-700">Login here</a></p>
        </div>
    </div>
    
</body>
</html>
