<?php
session_start();
include 'db.php';

// CSRF Protection: Generate a token
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32)); // Store CSRF token in session
}

// Set the default timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

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

// Handle search
$searchQuery = '';
$files = [];
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $searchQuery = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT files.*, categories.name AS category_name 
                           FROM files 
                           LEFT JOIN categories ON files.subcategory_id = categories.id 
                           WHERE files.filename LIKE ? 
                           ORDER BY files.upload_time DESC");
    $stmt->execute(['%' . $searchQuery . '%']);
    $files = $stmt->fetchAll();
} else if (isset($_GET['subcategory_id'])) {
    $subcategory_id = $_GET['subcategory_id'];
    $stmt = $pdo->prepare("SELECT files.*, categories.name AS category_name 
                           FROM files 
                           LEFT JOIN categories ON files.subcategory_id = categories.id 
                           WHERE files.subcategory_id = ? 
                           ORDER BY files.upload_time DESC");
    $stmt->execute([$subcategory_id]);
    $files = $stmt->fetchAll();
}



// Handle file deletion
if (isset($_GET['delete']) && isset($_SESSION['token']) && isset($_GET['token'])) {
    if ($_SESSION['token'] === $_GET['token']) {
        $fileId = $_GET['delete'];
        $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
        $stmt->execute([$fileId]);
        header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?')); // Redirect to avoid duplicate form submissions
        exit();
    } else {
        die('Invalid CSRF token.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Toggle subcategories and files
        function toggleSubcategories(parentId) {
            const subcategoryContainer = document.getElementById('subcategories_' + parentId);
            subcategoryContainer.classList.toggle('hidden');
        }

        function toggleFiles(subcategoryId) {
            const filesContainer = document.getElementById('files_' + subcategoryId);
            filesContainer.classList.toggle('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<!-- Header -->
<header class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 shadow-lg">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <h1 class="text-2xl font-bold">File Backup Management</h1>
        <!-- Search Form -->
        <form action="" method="GET" class="flex items-center">
            <input type="text" name="search" placeholder="Search files..." value="<?php echo htmlspecialchars($searchQuery); ?>" 
                class="p-2 rounded-lg text-gray-800" />
            <button type="submit" class="ml-2 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
                Search
            </button>
        </form>
    </div>
</header>

<!-- Main Content -->
<div class="max-w-6xl mx-auto p-6 bg-white shadow-lg rounded-lg mt-6">
    <div class="flex justify-center items-center">
        <h2 class="text-3xl font-semibold text-gray-800">Browse Files</h2>
    </div>

    <!-- Back to Dashboard Link -->
    <div class="mb-6">
        <a href="dashboard.php" class="text-blue-500 hover:underline">Back to Dashboard</a>
    </div>

<!-- Display search results or categories -->
<?php if (!empty($searchQuery)): ?>
    <h3 class="text-xl font-medium text-gray-700 mb-4">Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"</h3>
    <div class="space-y-4">
        <?php if (!empty($files)): ?>
            <?php foreach ($files as $file): ?>
                <div class="bg-white rounded-lg shadow p-4 flex justify-between items-center hover:shadow-md transition">
                    <div class="flex items-center space-x-4">
                        <!-- File Name -->
                        <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($file['filename']); ?></p>
                        
                        <!-- Category -->
                        <span class="text-xs text-gray-500"><?php echo htmlspecialchars($file['category_name']); ?></span>
                        
                        <!-- Upload Time -->
                        <span class="text-xs text-gray-500"><?php echo date('F j, Y, g:i A', strtotime($file['upload_time'])); ?> (PHT)</span>
                    </div>
                    <div class="flex gap-2">
                        <a href="<?php echo htmlspecialchars($file['filepath']); ?>" download 
                            class="bg-blue-500 text-white py-1 px-3 rounded-lg hover:bg-blue-600 transition">Download</a>
                        <a href="?delete=<?php echo $file['id']; ?>&token=<?php echo $_SESSION['token']; ?>" 
                            class="bg-red-500 text-white py-1 px-3 rounded-lg hover:bg-red-600 transition">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-500 italic">No files found for the search term.</p>
        <?php endif; ?>
    </div>

    <!-- Back to View All Files Link -->
    <div class="mt-6">
        <a href="view_files.php" class="text-blue-500 hover:underline">Back to View All Files</a>
    </div>
<?php else: ?>
        <?php foreach ($parentCategories as $parent): ?>
            <div class="mb-6">
                <!-- Parent Category -->
                <div class="flex items-center justify-between bg-gray-100 p-4 rounded-lg shadow hover:bg-gray-200 transition">
                    <div class="flex items-center gap-4">
                        <img src="https://cdn-icons-png.flaticon.com/512/148/148947.png" alt="Folder Icon" class="w-8 h-8">
                        <span class="text-xl font-medium text-gray-800"><?php echo htmlspecialchars($parent['name']); ?></span>
                    </div>
                    <button onclick="toggleSubcategories(<?php echo $parent['id']; ?>)" 
                        class="text-blue-600 hover:text-blue-800 font-semibold">Toggle Subcategories</button>
                </div>

<!-- Subcategories -->
<div id="subcategories_<?php echo $parent['id']; ?>" class="pl-6 mt-4 hidden">
    <?php if (isset($subcategories[$parent['id']])): ?>
        <?php foreach ($subcategories[$parent['id']] as $subcategory): ?>
            <div class="bg-gray-50 p-4 rounded-lg shadow hover:shadow-lg hover:bg-gray-100 transition">
                <div class="flex items-center gap-4">
                    <img src="https://cdn-icons-png.flaticon.com/512/715/715676.png" 
                        alt="Subfolder Icon" class="w-6 h-6">
                    <button onclick="toggleFiles(<?php echo $subcategory['id']; ?>)" 
                        class="text-lg font-medium text-gray-800 hover:text-gray-900">
                        <?php echo htmlspecialchars($subcategory['name']); ?>
                    </button>
                </div>

                <!-- Files in Subcategory (List Format) -->
                <div id="files_<?php echo $subcategory['id']; ?>" class="mt-4 space-y-2 hidden">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM files WHERE subcategory_id = ? ORDER BY upload_time DESC");
                    $stmt->execute([$subcategory['id']]);
                    $subcategoryFiles = $stmt->fetchAll();
                    ?>
                    <?php if (!empty($subcategoryFiles)): ?>
                        <ul class="space-y-4">
                            <?php foreach ($subcategoryFiles as $file): ?>
                                <li class="bg-white p-4 rounded-lg shadow hover:shadow-md transition flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <p class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($file['filename']); ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo date('F j, Y, g:i A', strtotime($file['upload_time'])); ?> (PHT)
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="<?php echo htmlspecialchars($file['filepath']); ?>" download 
                                            class="bg-blue-500 text-white py-1 px-3 rounded-lg hover:bg-blue-600 transition">Download</a>
                                        <a href="?delete=<?php echo $file['id']; ?>&token=<?php echo $_SESSION['token']; ?>" 
                                            class="bg-red-500 text-white py-1 px-3 rounded-lg hover:bg-red-600 transition">Delete</a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-gray-500 italic">No files found in this subcategory.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-500 italic">No subcategories available.</p>
    <?php endif; ?>
</div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
