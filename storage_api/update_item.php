<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

// Get raw input
$rawData = file_get_contents("php://input");
$data = json_decode($rawData);

// Get ID from URL parameter
$id = isset($_GET['id']) ? $_GET['id'] : null;

// Log untuk debugging
error_log("Update Item - ID: " . $id);
error_log("Raw data: " . $rawData);

if ($id && !empty($data->name) && !empty($data->description) && isset($data->quantity)) {
    try {
        $query = "UPDATE items 
                  SET name = :name, 
                      description = :description, 
                      quantity = :quantity 
                  WHERE id = :id";
        
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':quantity', $data->quantity, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Cek apakah ada row yang terpengaruh
            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Item updated successfully',
                    'affected_rows' => $stmt->rowCount()
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No changes made or item not found',
                    'id' => $id
                ]);
            }
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update item',
                'error' => $errorInfo
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Incomplete data or missing ID',
        'received' => [
            'id' => $id ?? 'missing',
            'name' => $data->name ?? 'missing',
            'description' => $data->description ?? 'missing',
            'quantity' => $data->quantity ?? 'missing'
        ]
    ]);
}
?>
