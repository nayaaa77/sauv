<?php
echo "<h1>Tes Koneksi API Komerce (cURL)</h1>";

// 1. Masukkan API Key "Shipping Cost" Anda di sini
$apiKey = 'SrZPSdpo43fc1282affbab5brmYUADKf'; 

// 2. Tentukan URL Endpoint untuk tes (Kita coba ambil daftar provinsi)
$url = 'https://rajaongkir.komerce.id/api/v1/destination/province';

// 3. Siapkan header, sesuai dokumentasi
$headers = [
    'key: ' . $apiKey
];

// 4. Inisialisasi cURL
$ch = curl_init();

// 5. Atur opsi cURL
curl_setopt($ch, CURLOPT_URL, $url); // Set URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Minta hasil sebagai string
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set header kustom kita
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // (Opsional) Nonaktifkan verifikasi SSL jika XAMPP Anda bermasalah

// 6. Eksekusi cURL dan ambil hasilnya
$response = curl_exec($ch);

// 7. Cek jika ada error cURL
if (curl_errno($ch)) {
    echo 'Error cURL: ' . curl_error($ch);
    exit;
}

// 8. Tutup cURL
curl_close($ch);

// 9. Tampilkan hasil (respon dari server)
echo "<h3>Respon mentah dari Server:</h3>";
echo $response;

// 10. Coba decode JSON dan tampilkan sebagai array yang rapi
echo "<h3>Respon dalam format Array (json_decode):</h3>";
$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";

?>