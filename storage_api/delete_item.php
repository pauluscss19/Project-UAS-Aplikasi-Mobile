<?php
require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    $query = "DELETE FROM items WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete item'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Item ID required'
    ]);
}
?>
