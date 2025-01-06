<?php
session_start();
include 'db.php';

// Create a new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = $_POST['name'];
    // Check if 'parent_id' exists in $_POST and set it to NULL if not
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? $_POST['parent_id'] : NULL; 

    // Check if the category already exists under the same parent
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE name = ? AND parent_id = ?");
    $stmt->execute([$name, $parent_id]);
    $existingCategory = $stmt->fetch();

    if ($existingCategory) {
        $error = "Category with this name already exists under the selected parent!";
    } else {
        // Insert category with the selected parent_id
        $stmt = $pdo->prepare("INSERT INTO categories (name, parent_id) VALUES (?, ?)");
        $stmt->execute([$name, $parent_id]);
        $success = "Category added successfully!";
    }
}

// Update category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    // Check if 'parent_id' exists in $_POST and set it to NULL if not
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? $_POST['parent_id'] : NULL;

    $stmt = $pdo->prepare("UPDATE categories SET name = ?, parent_id = ? WHERE id = ?");
    $stmt->execute([$name, $parent_id, $id]);
    $success = "Category updated successfully!";
}

// Delete category
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // First, delete all subcategories (cascade delete)
    $stmt = $pdo->prepare("DELETE FROM categories WHERE parent_id = ?");
    $stmt->execute([$id]);

    // Then delete the category itself
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Category deleted successfully!";
}

// Get all categories
$categories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-lg mt-10">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Manage Categories</h1>

        <!-- Back to Dashboard Link -->
        <div class="mb-6">
            <a href="dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Success Message -->
        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Add Category Form -->
        <form method="POST" class="mb-8">
            <input type="hidden" name="action" value="add">
            <div class="flex flex-col space-y-4">
                <input type="text" name="name" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter category name" required>
                
                <!-- Parent Category Dropdown -->
                <select name="parent_id" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Parent Category (Optional)</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="bg-blue-500 text-white font-semibold py-2 rounded-lg hover:bg-blue-600 transition duration-200">Add Category</button>
            </div>
        </form>

        <!-- Categories List in Folder/Subfolder format -->
        <div>
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Existing Categories</h2>
            <ul class="space-y-3">
                <?php
                // Get root categories (parent categories)
                $rootCategories = $pdo->query("SELECT * FROM categories WHERE parent_id IS NULL")->fetchAll();

                foreach ($rootCategories as $rootCategory): ?>
                    <li class="bg-gray-100 text-gray-700 rounded-lg px-4 py-3">
                        <strong><?php echo $rootCategory['name']; ?></strong> <!-- Folder -->

                        <!-- Edit Category Form -->
                        <form method="POST" class="mt-2 inline-block">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?php echo $rootCategory['id']; ?>">
                            <input type="text" name="name" value="<?php echo $rootCategory['name']; ?>" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <select name="parent_id" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Parent (Optional)</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $rootCategory['parent_id'] == $category['id'] ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="bg-yellow-500 text-white font-semibold py-2 rounded-lg hover:bg-yellow-600 transition duration-200">Update</button>
                        </form>

                        <!-- Delete Category Button -->
                        <a href="?delete=<?php echo $rootCategory['id']; ?>" class="bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 mt-2 inline-block">Delete</a>

                        <?php
                        // Get subcategories (subfolders) for each root category
                        $subCategories = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
                        $subCategories->execute([$rootCategory['id']]);
                        $subCategories = $subCategories->fetchAll();

                        if (!empty($subCategories)): ?>
                            <ul class="pl-6 mt-2 space-y-2">
                                <?php foreach ($subCategories as $subCategory): ?>
                                    <li class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2">
                                        <strong><?php echo $subCategory['name']; ?></strong> <!-- Subfolder -->

                                        <!-- Edit Subcategory Form -->
                                        <form method="POST" class="mt-2 inline-block">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="id" value="<?php echo $subCategory['id']; ?>">
                                            <input type="text" name="name" value="<?php echo $subCategory['name']; ?>" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <select name="parent_id" class="border border-gray-300 rounded-lg px-4 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="">Select Parent (Optional)</option>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>" <?php echo $subCategory['parent_id'] == $category['id'] ? 'selected' : ''; ?>><?php echo $category['name']; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="bg-yellow-500 text-white font-semibold py-2 rounded-lg hover:bg-yellow-600 transition duration-200">Update</button>
                                        </form>

                                        <!-- Delete Subcategory Button -->
                                        <a href="?delete=<?php echo $subCategory['id']; ?>" class="bg-red-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-red-600 mt-2 inline-block">Delete</a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</body>
</html>
