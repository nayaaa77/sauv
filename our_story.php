<?php include 'includes/header.php'; ?>

<style>
    .story-container {
        max-width: 800px; /* Lebar maksimal konten */
        margin: 40px auto; /* Posisi di tengah halaman dengan margin atas/bawah */
        padding: 0 20px; /* Jarak dari tepi layar pada mode mobile */
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        color: #333;
    }

    .story-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .story-header h1 {
        font-size: 2.5em; /* Ukuran font untuk 'About' */
        font-weight: 500;
        margin-bottom: 10px;
    }

    .story-header h2 {
        font-size: 1.2em;
        font-weight: 400;
        color: #666;
        margin-bottom: 30px;
    }

    /* === BAGIAN YANG DIUBAH ADA DI SINI === */
    .story-header p, .story-section p {
        font-size: 1.1em;
        line-height: 1.7;
        text-align: justify; /* Teks paragraf rata kanan-kiri */
    }

    .story-section {
        margin-bottom: 60px;
    }

    .story-section h2 {
        font-size: 2em; /* Ukuran font judul bagian */
        font-weight: 500;
        margin-bottom: 20px;
        text-align: left; /* Judul section tetap rata kiri */
    }

    .story-section img {
        width: 100%; /* Gambar responsif */
        height: auto;
        margin-bottom: 20px;
        border-radius: 4px; /* Sedikit lengkungan di sudut gambar */
    }
</style>

<main class="story-container">
    
    <div class="story-header">
        <h1>About</h1>
        <h2>Who we are and why we do what we do!</h2>
        <p>
            Welcome to Sauvatte, where our journey began not with a business plan, but with a deeply held passion for the art of adornment. In a world saturated with fleeting trends, we felt a calling to create something with more meaning—beautiful, high-quality pieces designed to be cherished and to stand the test of time.
        </p>
    </div>

    <section class="story-section">
        <h2>Our Philosophy</h2>
        
        <img src="./assets/img/contoh1.jpg" alt="Sauvatte jewelry collection">
        
        <p>
            Ultimately, Sauvatte is for the modern woman who finds strength in subtlety and confidence in classic style. Our collection is more than an offering of accessories; it's an invitation to invest in yourself and build a wardrobe of timeless items that tell your own unique story. We are honored to be a part of it.
        </p>
    </section>

    <section class="story-section">
        <h2>Produced with Care</h2>
        
        <img src="./assets/img/contoh2.jpg" alt="Craftsmanship at Sauvatte">
        
        <p>
            We champion the philosophy of 'slow fashion,' embracing the belief that true elegance is lasting, not disposable. This principle guides every decision we make, from the initial sketch to the final product. Our commitment is to meticulous craftsmanship, a promise we call 'Produced with Care.' We pour our hearts into the entire process, from hand-selecting premium materials that feel as good as they look, to perfecting the subtle details that make each design uniquely ours.
        </p>
    </section>

</main>

<?php include 'includes/footer.php'; ?>