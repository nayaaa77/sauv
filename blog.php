<?php 
// Sertakan header
include 'includes/header.php'; 
// Sertakan file koneksi database
require_once 'includes/db_conn.php';

// Query untuk mengambil semua data dari tabel blog, diurutkan dari yang terbaru
$result = $conn->query("SELECT id, title, content, created_at, image_url FROM blog ORDER BY created_at DESC");

$posts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Ambil postingan pertama sebagai featured post
$featured_post = array_shift($posts);
?>

<div class="container">
    <div class="blog-page-header">
        <h1>The Journal</h1>
        <p>Discover insights, stories, and inspiration behind our collections.</p>
    </div>

    <?php if ($featured_post): ?>
    <section class="featured-post-section">
        <a href="single_post.php?id=<?php echo $featured_post['id']; ?>" class="featured-post-card">
            <div class="featured-image-wrapper">
                <img src="./uploads/<?php echo rawurlencode(htmlspecialchars($featured_post['image_url'])); ?>" alt="<?php echo htmlspecialchars($featured_post['title']); ?>">
            </div>
            <div class="featured-content-wrapper">
                <p class="featured-date"><?php echo date('F d, Y', strtotime($featured_post['created_at'])); ?></p>
                <h2><?php echo htmlspecialchars($featured_post['title']); ?></h2>
                <p class="featured-excerpt">
                    <?php
                        // Ambil cuplikan dari konten, potong sekitar 150 karakter
                        $excerpt = strip_tags($featured_post['content']);
                        if (strlen($excerpt) > 150) {
                            $excerpt = substr($excerpt, 0, 150) . '...';
                        }
                        echo htmlspecialchars($excerpt);
                    ?>
                </p>
                <span class="read-more-link">Read More &rarr;</span>
            </div>
        </a>
    </section>
    <?php endif; ?>

    <?php if (!empty($posts)): ?>
    <section class="blog-grid-section">
        <div class="blog-grid">
            <?php foreach ($posts as $post): ?>
                <a href="single_post.php?id=<?php echo $post['id']; ?>" class="blog-post-card">
                    <div class="post-card-image">
                        <img src="./uploads/<?php echo rawurlencode(htmlspecialchars($post['image_url'])); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                    <div class="post-card-content">
                        <p class="post-card-date"><?php echo date('F d, Y', strtotime($post['created_at'])); ?></p>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>
    <?php elseif (!$featured_post): ?>
        <p style="text-align: center; padding: 50px 0;">Belum ada postingan yang tersedia.</p>
    <?php endif; ?>

</div>

<?php 
if ($conn) {
    $conn->close();
}
include 'includes/footer.php'; 
?>