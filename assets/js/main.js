document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================================
    // 1. LOGIC TAB LOGIN & REGISTER (PERBAIKAN UTAMA)
    // ==========================================================
    // Logika ini cocok dengan HTML "Sliding Tab" yang baru
    const authBtns = document.querySelectorAll('.toggle-btn');
    const authForms = document.querySelectorAll('.auth-form');

    if (authBtns.length > 0) {
        authBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Mencegah refresh jika tombol ada di dalam form (opsional)
                e.preventDefault(); 

                // 1. Hapus class 'active' dari semua tombol & form
                authBtns.forEach(b => b.classList.remove('active'));
                authForms.forEach(f => f.classList.remove('active'));

                // 2. Tambah class 'active' ke tombol yang sedang diklik
                this.classList.add('active');

                // 3. Ambil target ID dari atribut data-target="..." 
                const targetId = this.getAttribute('data-target');
                const targetForm = document.getElementById(targetId);

                // 4. Munculkan form yang sesuai
                if (targetForm) {
                    targetForm.classList.add('active');
                }
            });
        });
    }

    // ==========================================================
    // 2. LOGIC TAB DETAIL PRODUK
    // ==========================================================
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

    // ==========================================================
    // 3. LOGIC GALERI GAMBAR PRODUK
    // ==========================================================
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

    // ==========================================================
    // 4. LOGIC TOMBOL KUANTITAS (DETAIL PRODUK)
    // ==========================================================
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

    // ==========================================================
    // 5. LOGIC ACTIVE MENU NAVBAR
    // ==========================================================
    const navLinks = document.querySelectorAll('.nav-menu a');
    const currentPage = window.location.pathname.split("/").pop(); 

    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage) {
            link.classList.add('active');
        }
    });

    // ==========================================================
    // 6. LOGIC KUANTITAS DI KERANJANG (CART)
    // ==========================================================
    const cartItemsList = document.querySelector('.cart-items-list');

    if (cartItemsList) {
        cartItemsList.addEventListener('click', function(event) {
            const target = event.target;

            if (target.classList.contains('increase-qty-cart') || target.classList.contains('decrease-qty-cart')) {
                const productId = target.dataset.id;
                const quantityInput = document.querySelector(`.quantity-input-cart[data-id="${productId}"]`);
                let currentValue = parseInt(quantityInput.value, 10);
                const maxStock = parseInt(quantityInput.max, 10);

                if (target.classList.contains('increase-qty-cart')) {
                    if (currentValue < maxStock) {
                        quantityInput.value = currentValue + 1;
                    }
                } else if (target.classList.contains('decrease-qty-cart')) {
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                }
            }
        });
    }

    // ==========================================================
    // 7. LOGIC FORM ALAMAT BARU (CHECKOUT)
    // ==========================================================
    const addressChoiceRadios = document.querySelectorAll('input[name="address_choice"]');
    const newAddressForm = document.getElementById('new-address-form');
    const newAddressInputs = newAddressForm ? newAddressForm.querySelectorAll('input') : [];

    if (addressChoiceRadios.length > 0 && newAddressForm) {
        addressChoiceRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'new') {
                    newAddressForm.style.display = 'block';
                    newAddressInputs.forEach(input => input.required = true);
                } else {
                    newAddressForm.style.display = 'none';
                    newAddressInputs.forEach(input => input.required = false);
                }
            });
        });
    }

    // ==========================================================
    // 8. ANIMASI IKON KERANJANG SAAT ADD TO CART
    // ==========================================================
    const addToCartForm = document.querySelector('form[action="cart.php"]');
    const cartIcon = document.querySelector('.cart-icon-wrapper');

    if (addToCartForm && cartIcon) {
        const addToCartButton = addToCartForm.querySelector('button[name="add_to_cart"]');
        
        if (addToCartButton) {
            addToCartForm.addEventListener('submit', function(e) {
                if (addToCartButton.disabled) {
                    return;
                }

                e.preventDefault();
                cartIcon.classList.add('updated');

                cartIcon.addEventListener('animationend', () => {
                    cartIcon.classList.remove('updated');
                }, { once: true });

                setTimeout(() => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'add_to_cart';
                    hiddenInput.value = '1';
                    addToCartForm.appendChild(hiddenInput);

                    addToCartForm.submit();
                }, 400);
            });
        }
    }

    // ==========================================================
    // 9. MOBILE MENU (HAMBURGER) LOGIC [OPTIONAL]
    // ==========================================================
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu'); 

    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            const icon = this.querySelector('i');
            if (icon) {
                if (icon.classList.contains('fa-bars')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    }
});