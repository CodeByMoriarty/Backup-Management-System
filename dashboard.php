<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Backup Management - CRM</title>

    <!-- Tailwind CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- FontAwesome from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 dark:bg-gray-900 dark:text-white font-sans antialiased">

    <!-- Top Navigation Bar -->
    <div class="bg-gray-800 text-white p-4 shadow-md flex justify-between items-center rounded-b-xl">
        <h1 class="text-3xl font-semibold">File Backup Management</h1>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-6 py-3 rounded-lg font-medium transition-all">Logout</a>
    </div>

    <!-- Content Area -->
    <div class="flex flex-col min-h-screen px-6 py-8">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 mt-6">

            <!-- Dashboard Cards (Quick Access) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Manage Categories Card -->
                <div class="bg-blue-500 text-white rounded-lg shadow-lg p-8 transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <h3 class="text-2xl font-semibold">Manage Categories</h3>
                    <p class="mt-4 text-sm">Add, edit, and delete categories to organize your files efficiently.</p>
                    <a href="manage_categories.php" class="mt-6 inline-block bg-blue-700 hover:bg-blue-800 px-6 py-3 rounded-lg font-medium transition-all">Go to Categories</a>
                </div>

                <!-- Upload Files Card -->
                <div class="bg-green-500 text-white rounded-lg shadow-lg p-8 transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <h3 class="text-2xl font-semibold">Upload Files</h3>
                    <p class="mt-4 text-sm">Easily upload and store files with automatic timestamping to track your data.</p>
                    <a href="upload.php" class="mt-6 inline-block bg-green-700 hover:bg-green-800 px-6 py-3 rounded-lg font-medium transition-all">Upload Files</a>
                </div>

                <!-- View Files Card -->
                <div class="bg-yellow-500 text-white rounded-lg shadow-lg p-8 transform transition duration-300 hover:scale-105 hover:shadow-xl">
                    <h3 class="text-2xl font-semibold">View Files</h3>
                    <p class="mt-4 text-sm">Browse and manage all uploaded files stored in the system.</p>
                    <a href="view_files.php" class="mt-6 inline-block bg-yellow-700 hover:bg-yellow-800 px-6 py-3 rounded-lg font-medium transition-all">View Files</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
