<?php 
// Sertakan header
include 'includes/header.php'; 
// Sertakan file koneksi database
require_once 'includes/db_conn.php';
?>

<style>
    /* CSS yang disesuaikan agar cocok dengan gambar */
    .blog-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-top: 20px;
        margin-bottom: 40px;
    }
    .blog-post {
        text-decoration: none;
        color: #333;
        display: flex;
        flex-direction: column;
        overflow: hidden; /* DIUBAH: Penting untuk memotong gambar yang membesar */
    }
    .blog-post:hover img {
        opacity: 0.9;
        transform: scale(1); /* DIUBAH: Kembali ke ukuran normal saat di-hover */
    }
    .blog-post img {
        width: 100%;
        height: 350px;
        object-fit: cover;
        /* DIUBAH: Tambahkan transisi untuk transform dan atur keadaan awal gambar */
        transform: scale(1.05);
        transition: transform 0.4s ease, opacity 0.3s ease;
    }
    .blog-post-content {
        padding: 15px 5px;
    }
    .blog-post-content h3 {
        margin-top: 0;
        margin-bottom: 5px;
        font-size: 0.9rem;
        font-weight: 500;
        line-height: 1.4;
    }
    .blog-post-content .date {
        font-size: 0.9rem;
        color: #555;
    }
</style>

<div class="container">
    <h1 style="margin-top: 40px; text-align: center; margin-bottom: 30px;"></h1>

    <div class="blog-container">
        <?php
        // Query untuk mengambil semua data dari tabel blog, diurutkan dari yang terbaru
        $result = $conn->query("SELECT id, title, created_at, image_url FROM blog ORDER BY created_at DESC");

        if ($result && $result->num_rows > 0) :
            // Looping untuk setiap baris data
            while ($row = $result->fetch_assoc()) :
        ?>
                <a href="single_post.php?id=<?php echo $row['id']; ?>" class="blog-post">
                    <img src="./uploads/<?php echo rawurlencode(htmlspecialchars($row['image_url'])); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                    <div class="blog-post-content">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <span class="date">
                            <?php echo date('F d, Y', strtotime($row['created_at'])); ?>
                        </span>
                        </div>
                </a>
        <?php
            endwhile;
        else :
        ?>
            <p style="grid-column: 1 / -1; text-align: center;">Belum ada postingan yang tersedia.</p>
        <?php 
        endif; 
        // Selalu periksa apakah koneksi ada sebelum menutupnya
        if ($conn) {
            $conn->close();
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>