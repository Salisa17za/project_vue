<?php
include 'condb.php';

$response = ["success" => false, "message" => ""];

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // รับค่าข้อมูลพนักงาน
        $first_name = $_POST["first_name"];
        $last_name  = $_POST["last_name"];
        $username   = $_POST["username"];
        $password   = $_POST["password"];

        // ตรวจสอบว่ามีไฟล์รูปโปรไฟล์ส่งมาหรือไม่
        if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
            $upload_dir = "./uploads/"; // โฟลเดอร์เก็บรูป
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $filename = time() . "_" . basename($_FILES["profile_image"]["name"]);
            $target_file = $upload_dir . $filename;

            // อัปโหลดไฟล์รูป
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                // บันทึกข้อมูลลงฐานข้อมูล
                $stmt = $conn->prepare("INSERT INTO employees (first_name, last_name, username, password, profile_image) 
                                        VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $username, $password, $filename]);

                $response["success"] = true;
                $response["message"] = "บันทึกพนักงานเรียบร้อยแล้ว";
            } else {
                $response["message"] = "อัปโหลดไฟล์ไม่สำเร็จ";
            }
        } else {
            $response["message"] = "กรุณาเลือกรูปโปรไฟล์";
        }
    }
} catch (Exception $e) {
    $response["message"] = "เกิดข้อผิดพลาด: " . $e->getMessage();
}

echo json_encode($response);
?>
