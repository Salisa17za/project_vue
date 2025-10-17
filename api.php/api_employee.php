<?php

include 'condb.php';

$action = $_POST['action'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
    // เพิ่ม / แก้ไข / ลบ
    switch($action) {

        case 'add':
            // รับข้อมูลพนักงาน
            $first_name = $_POST['first_name'];
            $last_name  = $_POST['last_name'];
            $username   = $_POST['username'];
            $password   = $_POST['password'];

            // อัพโหลดไฟล์รูปโปรไฟล์
            $filename = null;
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
                $targetDir = "uploads/";  // โฟลเดอร์เก็บรูป
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $filename = time() . '_' . basename($_FILES['profile_image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);
            }

            // คำสั่ง SQL สำหรับเพิ่มพนักงาน
            $sql = "INSERT INTO employees (first_name, last_name, username, password,image)
                    VALUES (:first_name, :last_name, :username, :password, :image)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "เพิ่มพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "เพิ่มพนักงานล้มเหลว"]);
            }
            break;

        case 'update':
            $employee_id = $_POST['employee_id'];
            $first_name = $_POST['first_name'];
            $last_name  = $_POST['last_name'];
            $username   = $_POST['username'];
            $password   = $_POST['password'];

            // อัพโหลดไฟล์รูปโปรไฟล์ใหม่
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $targetDir = "uploads/";
                $filename = time() . '_' . basename($_FILES['image']['name']);
                $targetFile = $targetDir . $filename;
                move_uploaded_file($_FILES['image']['name'], $targetFile);
                $imageSQL = ", image = :image";
            } else {
                $imageSQL = "";
            }

            // คำสั่ง SQL สำหรับแก้ไขพนักงาน
            $sql = "UPDATE employees SET 
                        first_name = :first_name,
                        last_name = :last_name,
                        username = :username,
                        password = :password
                        $imageSQL
                    WHERE employee_id = :employee_id";
            $stmt = $conn->prepare($sql);

            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':employee_id', $employee_id);
            if (isset($filename)) $stmt->bindParam(':profile_image', $filename);

            if ($stmt->execute()) {
                echo json_encode(["message" => "แก้ไขข้อมูลพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "แก้ไขข้อมูลพนักงานล้มเหลว"]);
            }
            break;

        case 'delete':
            $employee_id = $_POST['employee_id'];
            $stmt = $conn->prepare("DELETE FROM employees WHERE employee_id = :employee_id");
            $stmt->bindParam(':employee_id', $employee_id);
            if ($stmt->execute()) {
                echo json_encode(["message" => "ลบพนักงานสำเร็จ"]);
            } else {
                echo json_encode(["error" => "ลบพนักงานล้มเหลว"]);
            }
            break;

        default:
            echo json_encode(["error" => "Action ไม่ถูกต้อง"]);
            break;
    }

} else {
    // GET: ดึงข้อมูลพนักงาน
    $stmt = $conn->prepare("SELECT * FROM employees ORDER BY employee_id DESC");
    if ($stmt->execute()) {
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $employees]);
    } else {
        echo json_encode(["success" => false, "data" => []]);
    }
}
?>
