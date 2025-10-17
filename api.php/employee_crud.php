<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $method = $_SERVER['REQUEST_METHOD'];

    // ✅ ดึงข้อมูลพนักงานทั้งหมด
    if ($method === "GET") {
        $stmt = $conn->prepare("SELECT employee_id, first_name, last_name, username, password ,  image FROM employees ORDER BY employee_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
    }

    // ✅ เพิ่มข้อมูลพนักงาน
    elseif ($method === "POST") {
        // ตรวจสอบว่าข้อมูลมาจาก JSON หรือ form-data
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';

        if (stripos($contentType, "application/json") !== false) {
            $data = json_decode(file_get_contents("php://input"), true);
        } else {
            $data = $_POST;
        }

        // ตรวจสอบค่าว่าง
        if (empty($data["first_name"]) || empty($data["last_name"]) || empty($data["username"]) || empty($data["password"]) || empty($data["image"])) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        // เพิ่มข้อมูลพนักงาน
        $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, username, password , image)
                                VALUES (:first_name, :last_name, :username, :password , :image)");

        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":username", $data["username"]);
        if (!empty($data["password"])) {
            $stmt->bindParam(":password", $password_hash);
        }
        $stmt->bindParam(":id", $customer_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
        $stmt->bindParam(":image", $data["image"]);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลพนักงานเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลพนักงานได้"]);
        }
    }

    // ✅ แก้ไขข้อมูลพนักงาน
    elseif ($method === "PUT") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["employee_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า employee_id"]);
            exit;
        }

        $employee_id = intval($data["employee_id"]);

        $sql = "UPDATE employees 
                SET first_name = :first_name, 
                    last_name = :last_name, 
                    username = :username, 
                    password = :$password_hash, 
                    image = :image
                WHERE employee_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $data["first_name"]);
        $stmt->bindParam(":last_name", $data["last_name"]);
        $stmt->bindParam(":username", $data["username"]);
       if (!empty($data["password"])) {
            $stmt->bindParam(":password", $password_hash);
        }
        $stmt->bindParam(":id", $customer_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
        $stmt->bindParam(":image", $data["image"]);
        $stmt->bindParam(":id", $employee_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลพนักงานเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลพนักงานได้"]);
        }
    }

    // ✅ ลบข้อมูลพนักงาน
    elseif ($method === "DELETE") {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data["employee_id"])) {
            echo json_encode(["success" => false, "message" => "ไม่พบค่า employee_id"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = :id");
        $stmt->bindParam(":id", $data["employee_id"], PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลพนักงานเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลพนักงานได้"]);
        }
    }

    else {
        echo json_encode(["success" => false, "message" => "Method ไม่ถูกต้อง"]);
    }

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
