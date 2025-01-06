<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (hash_equals($_SESSION['token'], $_POST['token'])) {
        $file_id = $_POST['file_id'];

        // Get file details
        $stmt = $pdo->prepare("SELECT filepath FROM files WHERE id = ?");
        $stmt->execute([$file_id]);
        $file = $stmt->fetch();

        if ($file) {
            // Delete the file from the server
            if (file_exists($file['filepath'])) {
                unlink($file['filepath']);
            }

            // Delete the file record from the database
            $stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
            $stmt->execute([$file_id]);

            $_SESSION['message'] = "File deleted successfully!";
        } else {
            $_SESSION['error'] = "File not found!";
        }
    } else {
        $_SESSION['error'] = "Invalid CSRF token!";
    }

    header('Location: file_manager.php');
    exit();
}
?>
