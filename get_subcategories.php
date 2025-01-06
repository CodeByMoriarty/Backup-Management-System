<?php
include 'db.php';

if (isset($_GET['category_id'])) {
    $categoryId = $_GET['category_id'];

    // Get subcategories for the selected category
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE parent_id = ?");
    $stmt->execute([$categoryId]);
    $subcategories = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subcategories);
}
?>
