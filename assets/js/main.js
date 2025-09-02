document.addEventListener('DOMContentLoaded', function() {
    // ===== Kode untuk toggle Login/Register =====
    const toggleButtons = document.querySelectorAll('.toggle-btn');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');

    if (toggleButtons.length > 0 && loginForm && registerForm) {
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

    // ===== Kode untuk Tombol Kuantitas Produk =====
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const quantityInput = document.getElementById('quantity');

    if (decreaseBtn && increaseBtn && quantityInput) {
        const maxStock = parseInt(quantityInput.max, 10);

        function updateButtonStates() {
            const currentValue = parseInt(quantityInput.value, 10);
            increaseBtn.disabled = currentValue >= maxStock;
            decreaseBtn.disabled = currentValue <= 1;
        }

        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue < maxStock) {
                quantityInput.value = currentValue + 1;
                updateButtonStates();
            }
        });

        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                updateButtonStates();
            }
        });

        quantityInput.addEventListener('input', function() {
            let currentValue = parseInt(quantityInput.value, 10);
            if (currentValue > maxStock) quantityInput.value = maxStock;
            if (currentValue < 1) quantityInput.value = 1;
            updateButtonStates();
        });

        updateButtonStates();
    }

    // ===== Kode untuk menandai menu navbar yang aktif =====
    const navLinks = document.querySelectorAll('.nav-menu a');
    const currentPage = window.location.pathname.split("/").pop(); // Mengambil nama file dari URL

    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });
});