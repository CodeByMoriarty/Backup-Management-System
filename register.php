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
    <link href="tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4">Register</h1>
        <form method="POST">
            <input type="text" name="username" class="border p-2 w-full mb-4" placeholder="Username" required>
            <input type="password" name="password" class="border p-2 w-full mb-4" placeholder="Password" required>
            <input type="password" name="confirm_password" class="border p-2 w-full mb-4" placeholder="Confirm Password" required>
            <button class="bg-blue-500 text-white py-2 px-4 rounded">Register</button>
            <?php if (isset($error)): ?>
                <p class="text-red-500 mt-2"><?php echo $error; ?></p>
            <?php elseif (isset($success)): ?>
                <p class="text-green-500 mt-2"><?php echo $success; ?></p>
            <?php endif; ?>
        </form>
        <a href="index.php" class="text-blue-500 mt-2 block">Back to Login</a>
    </div>
</body>
</html>
