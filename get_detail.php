<?php
include 'db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$cat_res = mysqli_query($conn, "SELECT * FROM CatBreeds WHERE id = $id");
$cat = mysqli_fetch_assoc($cat_res);
$img_res = mysqli_query($conn, "SELECT * FROM CatImages WHERE cat_id = $id");
?>
<h2 style="color: #6c5ce7;"><?= htmlspecialchars($cat['name_th']) ?></h2>
<p><strong>รายละเอียด:</strong> <?= nl2br(htmlspecialchars($cat['description'])) ?></p>
<p><strong>ลักษณะนิสัย:</strong> <?= nl2br(htmlspecialchars($cat['characteristics'])) ?></p>
<p><strong>การดูแล:</strong> <?= nl2br(htmlspecialchars($cat['care_instructions'])) ?></p>

<h4>🖼️ อัลบั้มรูปภาพ</h4>
<div class="gallery" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px;">
    <?php while($img = mysqli_fetch_assoc($img_res)): ?>
        <img src="<?= htmlspecialchars($img['image_url']) ?>" 
             style="width: 100%; height: 120px; object-fit: cover; border-radius: 10px; cursor: pointer;" 
             onclick="window.open(this.src)">
    <?php endwhile; ?>
</div>