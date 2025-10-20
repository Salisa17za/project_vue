<?php
  // เชื่อมต่อฐานข้อมูล
include 'condb.php';

try {
 //ตรวจสอบคำขอที่ได้รับจาก Client  ตามประเภทของคำ ว่าเป็น GET หรือ POST
    $method = $_SERVER['REQUEST_METHOD'];

   if ($method == 'POST') {
        // รับข้อมูลจาก Client
        $data = json_decode(file_get_contents("php://input"), true);

        // ตรวจสอบค่าที่จำเป็น
        if (isset($data['first_name'], $data['last_name'],  $data['username'], $data['password'], $data['image'])) {
            // เพิ่มข้อมูลลูกค้าใหม่
          $stmt = $conn->prepare("INSERT INTO employee (first_name, last_name, phone, username, password) VALUES (:first_name, :last_name,  :username, :password,:image)");
            $stmt->bindParam(':firstName', $data['firstName']);
            $stmt->bindParam(':last_name', $data['last_name']);
            $stmt->bindParam(':username', $data['username']);
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "employee added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Error adding employee"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Missing required fields"]);
        }
    } 
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}

?>