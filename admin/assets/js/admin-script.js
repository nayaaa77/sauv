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
        // (Catatan: Sepertinya ini lebih baik diletakkan di luar loop forEach)
        const tawkIconElement = document.getElementById('tawkIcon');
        if (tawkIconElement) {
            // Menggunakan ikon Font Awesome yang tersedia
            tawkIconElement.classList.add('fas', 'fa-comment-dots');
        }
    });

    // --- MULAI KODE BARU UNTUK STATUS ORDER ---
    
    // Ambil semua dropdown status di halaman (jika ada)
    const statusSelects = document.querySelectorAll('.status-badge');

    if (statusSelects.length > 0) {
        // Fungsi untuk memperbarui warna berdasarkan nilai
        function updateStatusColor(selectElement) {
            // 1. Hapus semua kelas status yang mungkin ada
            selectElement.classList.remove('status-pending', 'status-confirmed', 'status-shipped', 'status-completed', 'status-cancelled');
            
            // 2. Tambahkan kelas baru berdasarkan nilai yang dipilih
            selectElement.classList.add('status-' + selectElement.value);
        }

        // Terapkan fungsi ini ke setiap dropdown
        statusSelects.forEach(function(select) {
            // Tambahkan event listener 'change'
            select.addEventListener('change', function() {
                // 'this' mengacu pada elemen <select> yang diubah
                updateStatusColor(this); 
            });
        });
    }
    // --- SELESAI KODE BARU UNTUK STATUS ORDER ---

});