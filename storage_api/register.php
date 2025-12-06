<?php
require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
    
    // Check if email already exists
    $checkQuery = "SELECT id FROM users WHERE email = :email";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':email', $data->email);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email already exists'
        ]);
        exit();
    }
    
    $query = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
    $stmt = $db->prepare($query);
    
    $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
    
    $stmt->bindParam(':name', $data->name);
    $stmt->bindParam(':email', $data->email);
    $stmt->bindParam(':password', $hashedPassword);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Incomplete data'
    ]);
}
?>
