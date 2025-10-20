<?php
// เชื่อมต่อฐานข้อมูล
include 'condb.php';

header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === "GET") {
        // ✅ ดึงข้อมูลลูกค้าทั้งหมด
        $stmt = $conn->prepare("SELECT * FROM employee ORDER BY employee_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["success" => true, "data" => $result]);
    }
//เพิ่มข้อมูล
elseif ($method === "POST") {
        // ✅ เพิ่มข้อมูลลูกค้าใหม่
        $data = json_decode(file_get_contents("php://input"), true);

       

        $stmt = $conn->prepare("INSERT INTO employees (employee_id,first_name,last_name,username, password) 
                                VALUES (:employee_id,:first_name,:last_name,:username,:password)");
                                
        $stmt->bindParam(":student_id", $data["student_id"]);
        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":username", $data["username"]);
        $stmt->bindParam(":password", $data["password"]);
 
    

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลได้"]);
        }
    }


//ลบข้อมูล
    elseif ($method === "DELETE") {
        // ✅ ลบข้อมูลลูกค้าตาม 
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["employee_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า employee_id"]);
            exit;
        }

        $student_id = intval($data["employee_id"]);

        $stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = :id");
        $stmt->bindParam(":id", $employee_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลได้"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}

?>