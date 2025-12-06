<?php
require_once 'config.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->email) && !empty($data->password)) {
    $query = "SELECT id, name, email, password FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $data->email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($data->password, $row['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid password'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Incomplete data'
    ]);
}
?>
