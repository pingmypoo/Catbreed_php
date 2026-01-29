<?php
include 'db.php';
$sql = "SELECT b.*, (SELECT image_url FROM CatImages WHERE cat_id = b.id LIMIT 1) as thumb 
        FROM CatBreeds b 
        ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Management - Cat Breeds</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        .status-show { color: green; font-weight: bold; }
        .status-hide { color: red; }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <h3>🐱 ระบบจัดการข้อมูลสายพันธุ์แมว</h3>
        <div class="top-bar">
            <a href="add.php" style="text-decoration: none; background: #00b894; color: white; padding: 8px 15px; border-radius: 5px;">➕ เพิ่มข้อมูลใหม่</a>
            <a href="index.php" style="margin-left: 15px; color: #666;">🏠 ไปหน้าแรก</a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>รูปหน้าปก</th>
                <th>ชื่อ (TH)</th>
                <th>ชื่อ (EN)</th>
                <th>สถานะ</th>
                <th>จัดการ</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?= $row['id']; ?></td>
                <td>
                    <?php 
                    // เช็คว่ามีไฟล์จริงหรือไม่ก่อนแสดงผล
                    if ($row['thumb'] && file_exists($row['thumb'])) { 
                    ?>
                        <img src="<?= htmlspecialchars($row['thumb']) ?>?v=<?= time() ?>" width="80" height="60" style="object-fit: cover; border-radius: 4px; border: 1px solid #eee;">
                    <?php } else { ?>
                        <div style="width: 80px; height: 60px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #999;">ไม่มีรูป</div>
                    <?php } ?>
                </td>
                <td><strong><?= htmlspecialchars($row['name_th']); ?></strong></td>
                <td><?= htmlspecialchars($row['name_en']); ?></td>
                <td class="<?= $row['is_visible'] ? 'status-show' : 'status-hide' ?>">
                    <?= $row['is_visible'] ? '● แสดง' : '○ ซ่อน' ?>
                </td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">✏️ แก้ไข</a> |
                    <a href="delete.php?id=<?= $row['id'] ?>" style="color: red;" onclick="return confirm('ยืนยันการลบ?')">🗑️ ลบ</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>