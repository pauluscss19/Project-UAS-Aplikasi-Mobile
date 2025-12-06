<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

// Cek apakah database terkoneksi
if (!$db) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit();
}

// Get raw POST data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData);

// Log untuk debugging
error_log("Raw data: " . $rawData);
error_log("Decoded data: " . print_r($data, true));

if (!empty($data->name) && !empty($data->description) && isset($data->quantity)) {
    try {
        $query = "INSERT INTO items (name, description, quantity, created_at) 
                  VALUES (:name, :description, :quantity, NOW())";
        $stmt = $db->prepare($query);
        
        $stmt->bindParam(':name', $data->name);
        $stmt->bindParam(':description', $data->description);
        $stmt->bindParam(':quantity', $data->quantity, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $lastId = $db->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Item created successfully',
                'id' => $lastId,
                'debug' => [
                    'name' => $data->name,
                    'description' => $data->description,
                    'quantity' => $data->quantity
                ]
            ]);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode([
                'success' => false,
                'message' => 'Failed to execute query',
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
        'message' => 'Incomplete data',
        'received' => [
            'name' => $data->name ?? 'missing',
            'description' => $data->description ?? 'missing',
            'quantity' => $data->quantity ?? 'missing'
        ]
    ]);
}
?>
