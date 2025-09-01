<?php 
// Sertakan header
include 'includes/header.php'; 
// Sertakan file koneksi database
require_once 'includes/db_conn.php';

// Cek apakah ID ada di URL, jika tidak, alihkan ke halaman blog
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: blog.php');
    exit();
}

$post_id = intval($_GET['id']); // Ambil ID dan pastikan itu adalah integer

// Gunakan prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT title, content, image_url, created_at FROM blog WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) :
    $post = $result->fetch_assoc();
?>

<style>
    /* CSS disesuaikan dengan layout baru */
    .single-post-container {
        max-width: 720px; 
        margin: 60px auto;
        padding: 20px;
        text-align: center;
    }
    .post-date {
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 25px;
    }
    .post-title {
        font-size: 1.8rem;
        text-transform: uppercase; 
        margin-bottom: 40px;
        line-height: 1.4;
        letter-spacing: 0.5px;
    }
    .post-image {
        width: 100%;
        height: auto; /* Biarkan tinggi gambar menyesuaikan secara otomatis */
        /* Batasi tinggi gambar maks. 70% dari tinggi layar browser */
        max-height: 70vh; 
        /* HAPUS object-fit: cover agar gambar tidak terpotong */
        margin-bottom: 40px;
        border-radius: 0; 
    }
    .single-post-content {
        line-height: 1.8;
        font-size: 1.1rem;
        text-align: left; 
        white-space: pre-wrap;
    }
    .back-link {
        display: inline-block;
        margin-top: 40px;
        text-decoration: none;
        color: #007bff;
    }
</style>

<div class="container">
    <div class="single-post-container">
        <p class="post-date">
            <?php echo date('d F Y', strtotime($post['created_at'])); ?>
        </p>
        
        <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        
        <img class="post-image" src="./uploads/<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">

        <div class="single-post-content">
    <?php echo $post['content']; ?>
</div>
        
        <a href="blog.php" class="back-link">&larr; Kembali ke Blog</a>
    </div>
</div>

<?php 
else :
    // Jika post dengan ID tersebut tidak ditemukan
    echo '<div class="container" style="text-align:center; padding: 50px 0;"><h1>Post tidak ditemukan.</h1><a href="blog.php">Kembali ke Blog</a></div>';
endif;

$stmt->close();
$conn->close();

include 'includes/footer.php'; 
?>