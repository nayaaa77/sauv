document.addEventListener('DOMContentLoaded', function() {
    // Grafik Penjualan (Sales Chart)
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    // Ganti data ini dengan data dinamis dari database Anda via PHP
                    data: [1200000, 1900000, 3000000, 500000, 2500000, 3100000, 4500000, 2900000, 3300000, 5000000, 4800000, 6000000], 
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    }

    // Menandai menu sidebar yang aktif
    const currentPage = window.location.pathname.split("/").pop();
    const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }

        // Logika untuk menambahkan ikon Tawk.to secara dinamis
    const tawkIconElement = document.getElementById('tawkIcon');
    if (tawkIconElement) {
        // Menggunakan ikon Font Awesome yang tersedia
        tawkIconElement.classList.add('fas', 'fa-comment-dots');
    }
    });
});