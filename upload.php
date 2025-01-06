<?php
session_start();
include 'db.php';

// CSRF Protection: Generate a token
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // Store CSRF token in session
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload') {
    if ($_POST['token'] !== $_SESSION['token']) {
        die('Invalid CSRF token!');
    }

    // File details
    $file = $_FILES['file'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'] ?: NULL;  // Optional subcategory
    $user_id = 1; // Assuming you're assigning a default user_id (could be dynamic if user is logged in)

    // File upload logic
    if ($file['error'] != 0) {
        $error = "Error during file upload!";
    } else {
        // Use the original file name
        $uploadDir = 'uploads/';
        $fileName = basename($file['name']); // Use the original file name
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Insert file record into the database with associated category, user, and upload time
            $stmt = $pdo->prepare("INSERT INTO files (filename, filepath, upload_time, category_id, subcategory_id, user_id) VALUES (?, ?, NOW(), ?, ?, ?)");
            $stmt->execute([$fileName, $filePath, $category_id, $subcategory_id, $user_id]);
            $success = "File uploaded successfully!";
        } else {
            $error = "Failed to move uploaded file!";
        }
    }
}

// Get all categories (including both parent and subcategories)
$query = "SELECT * FROM categories ORDER BY parent_id, name";
$categories = $pdo->query($query)->fetchAll();

// Separate categories into parent and subcategories
$parentCategories = [];
$subcategories = [];

foreach ($categories as $category) {
    if ($category['parent_id'] === NULL) {
        $parentCategories[] = $category;  // Parent category
    } else {
        $subcategories[$category['parent_id']][] = $category;  // Subcategories by parent_id
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // JavaScript to fetch and show subcategories based on the selected category
        function loadSubcategories() {
            var categorySelect = document.getElementById('category_id');
            var subcategorySelect = document.getElementById('subcategory_id');
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';  // Clear subcategories

            var selectedCategory = categorySelect.value;

            if (selectedCategory) {
                // Display subcategories for the selected category
                var subcategories = <?php echo json_encode($subcategories); ?>;
                var selectedSubcategories = subcategories[selectedCategory];

                if (selectedSubcategories) {
                    selectedSubcategories.forEach(function(subcategory) {
                        var option = document.createElement('option');
                        option.value = subcategory.id;
                        option.textContent = subcategory.name;
                        subcategorySelect.appendChild(option);
                    });
                    subcategorySelect.style.display = 'block';  // Show subcategory dropdown
                } else {
                    subcategorySelect.style.display = 'none';  // Hide if no subcategories
                }
            } else {
                subcategorySelect.style.display = 'none';  // Hide subcategory dropdown if no category is selected
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">

    <div class="max-w-4xl mx-auto p-8 bg-white shadow-xl rounded-lg mt-12">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">Upload Your File</h1>

        <div class="mb-6">
            <a href="dashboard.php" class="text-blue-600 hover:underline font-medium">Back to Dashboard</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-6"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-6"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="upload">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

            <div class="flex flex-col space-y-4">
                <!-- File Upload -->
                <label for="file" class="text-gray-700 font-semibold">Choose a file to upload</label>
                <input type="file" name="file" id="file" class="border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>

                <!-- Category Selection -->
                <label for="category_id" class="text-gray-700 font-semibold">Select a Category</label>
                <select name="category_id" id="category_id" class="border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required onchange="loadSubcategories()">
                    <option value="">Select Category</option>
                    <?php 
                    // Loop through parent categories and display them
                    foreach ($parentCategories as $parent) {
                        echo "<option value='{$parent['id']}'>{$parent['name']}</option>";
                    }
                    ?>
                </select>

                <!-- Subcategory Selection (dynamic) -->
                <label for="subcategory_id" class="text-gray-700 font-semibold">Select a Subcategory</label>
                <select name="subcategory_id" id="subcategory_id" class="border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500" style="display: none;">
                    <option value="">Select Subcategory</option>
                </select>

                <!-- Submit Button -->
                <button type="submit" class="bg-blue-600 text-white font-semibold py-3 rounded-lg hover:bg-blue-700 transition duration-300">Upload File</button>
            </div>
        </form>
    </div>

</body>
</html>
