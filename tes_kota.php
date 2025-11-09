<?php
echo "<h1>Tes API Komerce (Ambil Kota)</h1>";

// 1. Masukkan API Key "Shipping Cost" Anda
$apiKey = 'SrZPSdpo43fc1282affbab5brmYUADKf'; // <-- Ganti dengan Key Anda

// 2. Tentukan URL Endpoint untuk ambil KOTA
// Kita akan coba ambil daftar kota untuk "JAWA BARAT" (yang memiliki ID 5 dari tes Anda sebelumnya)
$url = 'https://rajaongkir.komerce.id/api/v1/destination/city?province_id=5';

// 3. Siapkan header
$headers = [
    'key: ' . $apiKey
];

// 4. Inisialisasi cURL
$ch = curl_init();

// 5. Atur opsi cURL
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

// 6. Eksekusi cURL
$response = curl_exec($ch);

// 7. Cek jika ada error cURL
if (curl_errno($ch)) {
    echo 'Error cURL: ' . curl_error($ch);
    exit;
}

// 8. Tutup cURL
curl_close($ch);

// 9. Coba decode JSON dan tampilkan sebagai array
echo "<h3>Respon untuk Kota di Provinsi ID 5 (JAWA BARAT):</h3>";
$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";

?>