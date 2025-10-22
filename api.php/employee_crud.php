<?php
include 'condb.php';
header("Content-Type: application/json; charset=UTF-8");

try {
    $action = $_POST["action"] ?? "";

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $stmt = $conn->prepare("SELECT employee_id, first_name, last_name, username, image FROM employee ORDER BY employee_id DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $result]);
        exit;
    }

    // เพิ่มพนักงาน
    if ($action === "add") {
        $first_name = $_POST["first_name"] ?? "";
        $last_name = $_POST["last_name"] ?? "";
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";

        if (!$first_name || !$last_name || !$username || !$password) {
            echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ"]);
            exit;
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $image_filename = null;

        if (!empty($_FILES['image']['name'])) {
            $upload_dir = "uploads/";
            $image_filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $upload_dir . $image_filename;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        $stmt = $conn->prepare("INSERT INTO employee (first_name, last_name, username, password, image) 
                                VALUES (:first_name, :last_name, :username, :password, :image)");
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password_hash);
        $stmt->bindParam(":image", $image_filename);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => $stmt->execute() ? "เพิ่มข้อมูลพนักงานเรียบร้อย" : "ไม่สามารถเพิ่มข้อมูลได้"
        ]);
        exit;
    }

    // แก้ไขพนักงาน
    if ($action === "update") {
        $id = $_POST["employee_id"] ?? "";
        $first_name = $_POST["first_name"] ?? "";
        $last_name = $_POST["last_name"] ?? "";
        $username = $_POST["username"] ?? "";
        $password = $_POST["password"] ?? "";

        if (!$id) {
            echo json_encode(["success" => false, "message" => "ไม่พบรหัสพนักงาน"]);
            exit;
        }

        $password_hash = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
        $image_filename = null;

        if (!empty($_FILES['image']['name'])) {
            $upload_dir = "uploads/";
            $image_filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $target_file = $upload_dir . $image_filename;
            move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        }

        $sql = "UPDATE employee SET 
                first_name = :first_name, 
                last_name = :last_name, 
                username = :username";

        if ($password_hash) $sql .= ", password = :password";
        if ($image_filename) $sql .= ", image = :image";
        $sql .= " WHERE employee_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":first_name", $first_name);
        $stmt->bindParam(":last_name", $last_name);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($password_hash) $stmt->bindParam(":password", $password_hash);
        if ($image_filename) $stmt->bindParam(":image", $image_filename);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => $stmt->execute() ? "แก้ไขข้อมูลเรียบร้อย" : "ไม่สามารถแก้ไขข้อมูลได้"
        ]);
        exit;
    }

    // ลบพนักงาน
    if ($action === "delete") {
        $id = $_POST["employee_id"] ?? "";
        if (!$id) {
            echo json_encode(["success" => false, "message" => "ไม่พบรหัสพนักงาน"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM employee WHERE employee_id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        echo json_encode([
            "success" => $stmt->execute(),
            "message" => $stmt->execute() ? "ลบข้อมูลเรียบร้อย" : "ไม่สามารถลบข้อมูลได้"
        ]);
        exit;
    }

    echo json_encode(["success" => false, "message" => "ไม่พบ action ที่ร้องขอ"]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
