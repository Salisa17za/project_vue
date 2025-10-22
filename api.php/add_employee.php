<?php
include 'condb.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // รับค่าจาก $_POST แทน json
        if (isset($_POST['first_name'], $_POST['last_name'], $_POST['username'], $_POST['password']) && isset($_FILES['image'])) {

            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            // อัปโหลดรูปภาพ
            $image = $_FILES['image'];
            $image_name = uniqid() . "_" . basename($image["name"]);
            $target_dir = "uploads/";
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($image["tmp_name"], $target_file)) {
                // เตรียมคำสั่ง SQL
                $stmt = $conn->prepare("INSERT INTO employee (first_name, last_name, username, password, image) 
                                        VALUES (:first_name, :last_name, :username, :password, :image)");

                $stmt->bindParam(':first_name', $first_name);
                $stmt->bindParam(':last_name', $last_name);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':image', $image_name); // บันทึกชื่อไฟล์

                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "เพิ่มพนักงานเรียบร้อยแล้ว"]);
                } else {
                    echo json_encode(["success" => false, "message" => "ไม่สามารถเพิ่มข้อมูลได้"]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "อัปโหลดรูปภาพไม่สำเร็จ"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "ข้อมูลไม่ครบถ้วน"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
