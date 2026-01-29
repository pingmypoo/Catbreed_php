<?php
include 'db.php';
// เปิดการเช็ค Error เพื่อดูว่าติดตรงไหน (ลบออกเมื่อใช้งานจริง)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// ลบรูปภาพ
if (isset($_GET['del_img'])) {
    $img_id = intval($_GET['del_img']);
    mysqli_query($conn, "DELETE FROM CatImages WHERE id = $img_id");
    header("Location: edit.php?id=$id");
    exit;
}

if (isset($_POST['submit'])) {
    $name_th = mysqli_real_escape_string($conn, $_POST['name_th']);
    $name_en = mysqli_real_escape_string($conn, $_POST['name_en']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $characteristics = mysqli_real_escape_string($conn, $_POST['characteristics']); 
    $care_instructions = mysqli_real_escape_string($conn, $_POST['care_instructions']); 
    $is_visible = intval($_POST['is_visible']);

    // อัปเดตข้อมูลหลัก
    $sql_update = "UPDATE CatBreeds SET 
                    name_th='$name_th', 
                    name_en='$name_en', 
                    description='$description', 
                    characteristics='$characteristics', 
                    care_instructions='$care_instructions', 
                    is_visible='$is_visible' 
                   WHERE id=$id";
    
    if (!mysqli_query($conn, $sql_update)) {
        die("Error updating record: " . mysqli_error($conn));
    }

    // จัดการรูปภาพ
    if (!empty($_FILES['images']['name'][0])) {
        $folder = "images/";
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] == 0) {
                // 🔑 เปลี่ยนชื่อไฟล์ไม่ให้ซ้ำกัน
                $ext = pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
                $new_filename = "cat_" . time() . "_" . uniqid() . "." . $ext;
                $path = $folder . $new_filename;

                if (move_uploaded_file($tmp_name, $path)) {
                    // 🔑 บันทึกลงตาราง CatImages (เช็คตัวเล็กตัวใหญ่ของชื่อตารางให้ดีครับ)
                    $sql_img = "INSERT INTO CatImages (cat_id, image_url) VALUES ($id, '$path')";
                    if (!mysqli_query($conn, $sql_img)) {
                        echo "DB Error: " . mysqli_error($conn); // ถ้าบันทึกเข้า DB ไม่ได้จะฟ้องตรงนี้
                    }
                } else {
                    echo "ไม่สามารถย้ายไฟล์ไปยังโฟลเดอร์ images ได้ (เช็ค Permission 777)";
                }
            }
        }
    }
    // ถ้าไม่มี Error จะส่งกลับหน้าจัดการ
    echo "<script>alert('บันทึกเรียบร้อย'); window.location='management.php';</script>";
    exit;
}

// ดึงข้อมูลมาแสดงในฟอร์ม
$res = mysqli_query($conn, "SELECT * FROM CatBreeds WHERE id = $id");
$row = mysqli_fetch_assoc($res);
$images = mysqli_query($conn, "SELECT * FROM CatImages WHERE cat_id = $id");
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูล - <?= htmlspecialchars($row['name_th']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .img-preview { width: 110px; height: 110px; object-fit: cover; border-radius: 10px; border: 2px solid #eee; }
        .img-item { position: relative; display: inline-block; margin: 8px; }
        .del-x { position: absolute; top: -8px; right: -8px; background: #ff4757; color: white; border-radius: 50%; width: 22px; height: 22px; text-align: center; line-height: 20px; text-decoration: none; font-size: 14px; font-weight: bold; border: 2px solid white; }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: 30px auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <h2 style="color: #6c5ce7; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px;">✏️ แก้ไขข้อมูลสายพันธุ์แมว</h2>
        
        <form method="post" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>ชื่อ (ภาษาไทย)</label>
                    <input type="text" name="name_th" value="<?= htmlspecialchars($row['name_th']) ?>" required>
                </div>
                <div>
                    <label>ชื่อ (ภาษาอังกฤษ)</label>
                    <input type="text" name="name_en" value="<?= htmlspecialchars($row['name_en']) ?>" required>
                </div>
            </div>

            <label>คำอธิบายสายพันธุ์</label>
            <textarea name="description" style="height: 100px;"><?= htmlspecialchars($row['description']) ?></textarea>

            <label>ลักษณะนิสัย</label>
            <textarea name="characteristics" style="height: 80px;"><?= htmlspecialchars($row['characteristics']) ?></textarea>

            <label>การดูแลรักษา</label>
            <textarea name="care_instructions" style="height: 80px;"><?= htmlspecialchars($row['care_instructions']) ?></textarea>

            <label style="margin-top: 20px; display: block;">🖼️ รูปภาพในอัลบั้มปัจจุบัน</label>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 12px; margin-bottom: 20px; min-height: 50px;">
                <?php if(mysqli_num_rows($images) > 0): ?>
                    <?php while($img = mysqli_fetch_assoc($images)): ?>
                        <div class="img-item">
                            <img src="<?= htmlspecialchars($img['image_url']) ?>" class="img-preview">
                            <a href="?id=<?= $id ?>&del_img=<?= $img['id'] ?>" class="del-x" onclick="return confirm('ลบรูปนี้?')">×</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center;">ยังไม่มีรูปภาพ</p>
                <?php endif; ?>
            </div>

            <label>➕ เพิ่มรูปภาพใหม่ (เลือกได้หลายไฟล์)</label>
            <input type="file" name="images[]" multiple accept="image/*" style="margin-bottom: 20px;">

            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                <div>
                    <label>สถานะการแสดงผล:</label>
                    <select name="is_visible" style="width: 150px;">
                        <option value="1" <?= $row['is_visible'] == 1 ? 'selected' : '' ?>>เปิดการแสดงผล</option>
                        <option value="0" <?= $row['is_visible'] == 0 ? 'selected' : '' ?>>ซ่อนข้อมูล</option>
                    </select>
                </div>
                <div>
                    <button type="submit" name="submit" style="background: #6c5ce7; color: white; padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">💾 บันทึกการเปลี่ยนแปลง</button>
                    <a href="management.php" style="margin-left: 15px; color: #636e72; text-decoration: none;">ยกเลิก</a>
                </div>
            </div>
        </form>
    </div>
</body>
</html>