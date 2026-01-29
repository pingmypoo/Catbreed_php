<?php
include 'db.php';
// ดึงข้อมูลแมว และดึงรูปแรกสุดมาเป็นรูปหน้าปก
$sql = "SELECT b.*, (SELECT image_url FROM CatImages WHERE cat_id = b.id LIMIT 1) as thumb 
        FROM CatBreeds b WHERE is_visible = 1 ORDER BY b.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat Breeds World</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2d3436;
            --accent-color: #6c5ce7;
            --bg-body: #f1f2f6;
            --white: #ffffff;
            --shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--bg-body);
            margin: 0;
            color: var(--primary-color);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 50px 20px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 60px;
        }

        .header-section h1 {
            font-size: 3rem;
            margin: 0;
            background: linear-gradient(45deg, #6c5ce7, #a29bfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-section p {
            color: #636e72;
            font-size: 1.1rem;
            margin-top: 10px;
        }

        /* 🔑 Grid Layout */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 35px;
        }

        /* 🐱 Cat Card Style */
        .cat-card {
            background: var(--white);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            border: 1px solid rgba(255,255,255,0.3);
        }

        .cat-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        /* 🖼 Fixed Aspect Ratio (4:3) */
        .thumb-wrapper {
            position: relative;
            width: 100%;
            padding-top: 75%; /* 4:3 Ratio */
            overflow: hidden;
            background: #dfe6e9;
        }

        .thumb-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover; /* ทำให้รูป Fit กล่องอัตโนมัติ */
            object-position: center;
            transition: transform 0.5s ease;
        }

        .cat-card:hover .thumb-wrapper img {
            transform: scale(1.1);
        }

        .cat-info {
            padding: 25px;
            flex-grow: 1;
        }

        .cat-info h3 {
            margin: 0 0 12px 0;
            font-size: 1.4rem;
            color: #2d3436;
        }

        .cat-info p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #636e72;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* 🔘 Modern Button */
        .btn-detail {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 15px;
            font-family: 'Kanit', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
            border-radius: 0 0 25px 25px;
        }

        .btn-detail:hover {
            background: #5649c0;
        }

        /* 🪄 Modal Style */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(45, 52, 54, 0.9);
            backdrop-filter: blur(8px);
            overflow-y: auto;
        }

        .modal-content {
            background: var(--white);
            margin: 5% auto;
            padding: 40px;
            width: 90%;
            max-width: 850px;
            border-radius: 30px;
            position: relative;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .close-btn {
            position: absolute;
            right: 25px;
            top: 20px;
            font-size: 35px;
            cursor: pointer;
            color: #b2bec3;
            transition: color 0.2s;
        }

        .close-btn:hover { color: #d63031; }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 25px;
        }

        .gallery img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 15px;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .gallery img:hover { transform: scale(1.05); }

    </style>
</head>
<body>

<div class="container">
    <div class="header-section">
        <h1>Cat Breeds World</h1>
        <p>สารานุกรมแมวเหมียวฉบับสมบูรณ์ พร้อมรูปภาพหลายมุมมอง</p>
    </div>

    <div class="cat-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="cat-card">
                <div class="thumb-wrapper">
                    <img src="<?= $row['thumb'] ?: 'https://via.placeholder.com/400x300?text=No+Image' ?>" loading="lazy">
                </div>
                <div class="cat-info">
                    <h3><?= htmlspecialchars($row['name_th']) ?></h3>
                    <p><?= htmlspecialchars($row['description']) ?></p>
                </div>
                <button class="btn-detail" onclick="openCatDetail(<?= $row['id'] ?>)">อ่านรายละเอียดเพิ่มเติม</button>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<div id="catModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div id="modalData">กำลังโหลดข้อมูลสุดน่ารัก...</div>
    </div>
</div>

<script>
function openCatDetail(id) {
    const modal = document.getElementById('catModal');
    const content = document.getElementById('modalData');
    modal.style.display = "block";
    document.body.style.overflow = "hidden"; // ล็อกการสกロールหน้าหลัก

    fetch('get_detail.php?id=' + id)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
        });
}

function closeModal() {
    document.getElementById('catModal').style.display = "none";
    document.body.style.overflow = "auto";
}

window.onclick = function(event) {
    if (event.target == document.getElementById('catModal')) closeModal();
}
</script>
</body>
</html>