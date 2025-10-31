<?php


include 'condb.php'; // ต้องมี PDO connection เช่น $conn = new PDO(...)

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || !isset($data['username']) || !isset($data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid input data."
    ]);
    exit;
}

$username = trim($data['username']);
$password = trim($data['password']);

try {
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = ? ");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      
        // ✅ ตรวจสอบรหัสผ่านที่เข้ารหัสไว้ในฐานข้อมูล
        if (password_verify($password, $user['password'])) {
            echo json_encode([
                "success" => true,
                "message" => "Login successful.",
                "user" => [
                    "id" => $user['customer_id'],
                    "username" => $user['username']
                ]
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "รหัสผ่านไม่ถูกต้อง"
            ]);
        }


    } else {
        echo json_encode([
            "success" => false,
            "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error: " . $e->getMessage()
    ]);
}
?>