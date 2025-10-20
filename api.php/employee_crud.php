<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $action = $_POST["action"] ?? "";

    // ✅ ดึงข้อมูลพนักงานทั้งหมด (GET)
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $stmt = $conn->prepare("SELECT employee_id, first_name, last_name, username, password FROM employee ORDER BY employee_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
        exit;
    }

    // ✅ เพิ่มข้อมูลพนักงาน
    if ($action === "add") {
        $first_name = $_POST["first_name"] ?? "";
        $last_name = $_POST["last_name"] ?? "";
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";

        if (empty($first_name) || empty($last_name) || empty($username) || empty($password)) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        // เข้ารหัสรหัสผ่าน
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO employee (first_name, last_name, username, password) 
                                VALUES (:first_name, :last_name, :username, :password)");
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password_hash);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "เพิ่มข้อมูลพนักงานเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลพนักงานได้"]);
        }
        exit;
    }

    // ✅ แก้ไขข้อมูลพนักงาน
    if ($action === "update") {
        $employee_id = $_POST["employee_id"] ?? "";
        $first_name = $_POST["first_name"] ?? "";
        $last_name = $_POST["last_name"] ?? "";
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";

        if (empty($employee_id)) {
            echo json_encode(["success" => false, "message" => "ไม่พบรหัสพนักงาน"]);
            exit;
        }

        // ถ้ามีรหัสผ่านใหม่ ให้เข้ารหัส
        $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

        if ($password_hash) {
            $sql = "UPDATE employee 
                    SET first_name = :first_name, last_name = :last_name, username = :username, password = :password
                    WHERE employee_id = :id";
        } else {
            $sql = "UPDATE employee 
                    SET first_name = :first_name, last_name = :last_name, username = :username
                    WHERE employee_id = :id";
        }

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":id", $employee_id, PDO::PARAM_INT);
        if ($password_hash) $stmt->bindParam(":password", $password_hash);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
        exit;
    }

    // ✅ ลบข้อมูลพนักงาน
    if ($action === "delete") {
        $employee_id = $_POST["employee_id"] ?? "";

        if (empty($employee_id)) {
            echo json_encode(["success" => false, "message" => "ไม่พบรหัสพนักงาน"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM employee WHERE employee_id = :id");
        $stmt->bindParam(":id", $employee_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "ลบข้อมูลพนักงานเรียบร้อย"]);
        } else {
            echo json_encode(["success" => false, "message" => "ไม่สามารถลบข้อมูลพนักงานได้"]);
        }
        exit;
    }

    echo json_encode(["success" => false, "message" => "Action ไม่ถูกต้อง"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>
