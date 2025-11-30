</main> <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-links">
                    <a href="#">TERMS OF SERVICES</a>
                    <a href="#">SHIPPING AND RETURNS</a>
                </div>
            </div>
            <hr class="footer-divider">
            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy; <?php echo date('Y'); ?> Sauvatte. Terms of use and privacy policy.
                </div>
                <div class="footer-social">
                    <a href="https://www.instagram.com/sauvatte?igsh=dmRucWZ2MjV3OTVr" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    
                    <a href="https://www.tiktok.com/@sauvatte?_t=ZS-90jpTCoSnqv&_r=1" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <div class="mobile-bottom-nav">
        
        <a href="index.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>

        <a href="shop.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-bag"></i>
            <span>Shop</span>
        </a>

        <a href="blog.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : ''; ?>">
            <i class="fas fa-newspaper"></i>
            <span>Blog</span>
        </a>

        <a href="our_story.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'our_story.php' ? 'active' : ''; ?>">
            <i class="fas fa-book-open"></i>
            <span>Story</span>
        </a>

        <a href="my_account.php" class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'my_account.php' ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Account</span>
        </a>

    </div>

    <script src="assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script type="text/javascript">
    var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();

    // --- LOGIKA POSISI KHUSUS MOBILE ---
    Tawk_API.onLoad = function(){
        if(window.innerWidth < 768){
            // Suntikkan CSS agar chat naik ke atas menu bawah
            var style = document.createElement('style');
            style.innerHTML = `
                iframe[title="chat widget"], 
                div[id*="tawk"] { 
                    bottom: 90px !important; 
                }
            `;
            document.head.appendChild(style);
        }
    };
    // -----------------------------------

    (function(){
    var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
    s1.async=true;
    s1.src='https://embed.tawk.to/68da958471718d194ebbbc40/1j6auttha';
    s1.charset='UTF-8';
    s1.setAttribute('crossorigin','*');
    s0.parentNode.insertBefore(s1,s0);
    })();
    </script>
</body>
</html>