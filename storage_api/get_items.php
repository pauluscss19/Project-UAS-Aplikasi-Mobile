<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

try {
    $query = "SELECT id, name, description, quantity, created_at 
              FROM items 
              ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $items = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $items[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $items,
        'count' => count($items)
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage(),
        'data' => []
    ]);
}
?>
