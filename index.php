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
    <link href="tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white p-6 rounded shadow-md">
        <h1 class="text-2xl font-bold mb-4">Login</h1>
        <form method="POST">
            <input type="text" name="username" class="border p-2 w-full mb-4" placeholder="Username" required>
            <input type="password" name="password" class="border p-2 w-full mb-4" placeholder="Password" required>
            <button class="bg-blue-500 text-white py-2 px-4 rounded">Login</button>
            <?php if (isset($error)): ?>
                <p class="text-red-500 mt-2"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
