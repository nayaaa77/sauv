document.addEventListener('DOMContentLoaded', function() {
    // ===== Kode untuk toggle Login/Register =====
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (toggleButtons.length > 0 && loginForm && registerForm) {
        // ... (logika login/register tetap sama)
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                toggleButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                if (this.dataset.form === 'login') {
                    loginForm.style.display = 'block';
                    registerForm.style.display = 'none';
                } else {
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'block';
                }
            });
        });
    }

    // ===== Kode untuk Tab Detail Produk =====
    const tabLinks = document.querySelectorAll('.product-tabs .tab-link');
    const tabPanes = document.querySelectorAll('.product-tabs .tab-pane');

    if (tabLinks.length > 0 && tabPanes.length > 0) {
        // ... (logika tab produk tetap sama)
        const activateTabFromHash = () => {
            const currentHash = window.location.hash;
            if (currentHash) {
                const targetPane = document.querySelector(currentHash);
                const targetLink = document.querySelector(`.tab-link[href="${currentHash}"]`);
                if (targetPane && targetLink) {
                    tabLinks.forEach(link => link.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));
                    
                    targetLink.classList.add('active');
                    targetPane.classList.add('active');
                }
            }
        };
        activateTabFromHash();
        tabLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                const targetId = this.getAttribute('href');
                
                tabLinks.forEach(l => l.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');
                document.querySelector(targetId).classList.add('active');
                
                history.pushState(null, null, targetId);
            });
        });
    }

    // ===== Kode untuk Galeri Gambar Produk =====
    const thumbnails = document.querySelectorAll('.thumbnail-item');
    const mainImage = document.getElementById('main-image');

    if (thumbnails.length > 0 && mainImage) {
        // ... (logika galeri gambar tetap sama)
        if (thumbnails[0]) {
            thumbnails[0].classList.add('active');
        }
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const newImageSrc = this.getAttribute('src');
                mainImage.setAttribute('src', newImageSrc);
                thumbnails.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });
    }

    // ===== Kode untuk Tombol Kuantitas (BARU DITAMBAHKAN) =====
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const quantityInput = document.getElementById('quantity');

    // Pastikan elemen kuantitas ada di halaman ini
    if (decreaseBtn && increaseBtn && quantityInput) {
        // Event listener untuk tombol TAMBAH (+)
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            quantityInput.value = currentValue + 1;
        });

        // Event listener untuk tombol KURANG (-)
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            // Pastikan nilai tidak kurang dari 1
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        });
    }
});