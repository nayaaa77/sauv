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
    /* === BAGIAN CSS YANG DIPERBARUI === */
    .post-layout-wrapper {
        display: flex;
        align-items: flex-start;
        gap: 20px;
        margin-top: 40px;
    }
    .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 1px solid #e5e5e5;
        border-radius: 50%;
        color: #333;
        text-decoration: none;
        font-size: 16px;
        flex-shrink: 0; /* Mencegah tombol menyusut */
        transition: all 0.2s ease;
    }
    .btn-back:hover {
        background-color: #f7f7f7;
    }
    .single-post-container {
        flex: 1; /* Konten akan mengambil sisa ruang */
        max-width: 720px; 
        margin: 0 auto; /* Tetap di tengah dalam sisa ruang */
        padding: 0 20px;
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
        height: auto;
        max-height: 70vh; 
        margin-bottom: 40px;
        border-radius: 0; 
    }
    .single-post-content {
        line-height: 1.8;
        font-size: 1.1rem;
        text-align: left; 
    }
    /* Link kembali di bawah sudah tidak diperlukan, jadi bisa dihapus */
</style>

<div class="container">
    <div class="post-layout-wrapper">
        <a href="javascript:history.back()" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>

        <div class="single-post-container">
            <p class="post-date">
                <?php echo date('d F Y', strtotime($post['created_at'])); ?>
            </p>
            
            <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <img class="post-image" src="./uploads/<?php echo rawurlencode($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
    
            <div class="single-post-content">
                <?php echo $post['content']; ?>
            </div>
            
            </div>
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