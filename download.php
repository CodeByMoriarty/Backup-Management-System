<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    $success = "Category added successfully!";
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link href="tailwind.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <h1 class="text-2xl font-bold mb-4">Manage Categories</h1>
    <form method="POST">
        <input type="text" name="name" class="border p-2 w-full mb-4" placeholder="Category Name" required>
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Add Category</button>
    </form>
    <?php if (isset($success)): ?>
        <p class="text-green-500 mt-2"><?php echo $success; ?></p>
    <?php endif; ?>
    <ul class="mt-4">
        <?php foreach ($categories as $category): ?>
            <li class="border-b py-2"><?php echo $category['name']; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
