<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $name_th = mysqli_real_escape_string($conn, $_POST['name_th']);
    $name_en = mysqli_real_escape_string($conn, $_POST['name_en']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $characteristics = mysqli_real_escape_string($conn, $_POST['characteristics']);
    $care_instructions = mysqli_real_escape_string($conn, $_POST['care_instructions']);
    $is_visible = intval($_POST['is_visible']);

    $sql = "INSERT INTO CatBreeds (name_th, name_en, description, characteristics, care_instructions, is_visible) 
            VALUES ('$name_th', '$name_en', '$description', '$characteristics', '$care_instructions', '$is_visible')";
    
    if (mysqli_query($conn, $sql)) {
        $cat_id = mysqli_insert_id($conn);
        
        if (!empty($_FILES['images']['name'][0])) {
            $folder = "images/";
            // 🔑 สร้างโฟลเดอร์ถ้าไม่มี และตั้ง Permission เป็น 0777
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }
            
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                    $new_filename = time() . "_" . uniqid() . "." . $ext;
                    $path = $folder . $new_filename;
                    
                    // 🔑 ย้ายไฟล์สำเร็จก่อน ถึงจะบันทึกลง Database
                    if (move_uploaded_file($tmp_name, $path)) {
                        $sql_img = "INSERT INTO CatImages (cat_id, image_url) VALUES ($cat_id, '$path')";
                        mysqli_query($conn, $sql_img);
                    } else {
                        // ถ้าบันทึกไม่ได้ ให้หยุดดู error (ลบออกเมื่อใช้งานจริง)
                        die("ไม่สามารถย้ายไฟล์ไปที่โฟลเดอร์ images ได้ กรุณาเช็ค Permission โฟลเดอร์");
                    }
                }
            }
        }
    }
    header("Location: management.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เพิ่มสายพันธุ์แมว</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <h3>➕ เพิ่มสายพันธุ์แมว (ระบบสุ่มชื่อไฟล์)</h3>
        <form method="post" enctype="multipart/form-data">
            <label>ชื่อสายพันธุ์ (TH)</label><input type="text" name="name_th" required>
            <label>ชื่อสายพันธุ์ (EN)</label><input type="text" name="name_en" required>
            <label>คำอธิบาย</label><textarea name="description" required></textarea>
            <label>ลักษณะนิสัย</label><textarea name="characteristics"></textarea>
            <label>การดูแล</label><textarea name="care_instructions"></textarea>
            <label>เลือกรูปภาพ (เลือกได้หลายไฟล์)</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <label>สถานะการแสดงผล</label>
            <select name="is_visible"><option value="1">แสดง</option><option value="0">ซ่อน</option></select>
            <button type="submit" name="submit">💾 บันทึกข้อมูล</button>
            <a href