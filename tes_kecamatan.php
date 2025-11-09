<?php
echo "<h1>Tes API Komerce (Ambil Kecamatan)</h1>";

// 1. Masukkan API Key "Shipping Cost" Anda
$apiKey = 'SrZPSdpo43fc1282affbab5brmYUADKf'; // <-- Ganti dengan Key Anda

// 2. Tentukan URL Endpoint untuk ambil KECAMATAN (District)
// Kita gunakan ID Kota BANDUNG (ID: 55) dari tes sebelumnya
$url = 'https://rajaongkir.komerce.id/api/v1/destination/district/77';

// 3. Siapkan header
$headers = [
    'key: ' . $apiKey
];

// 4. Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

// 5. Eksekusi cURL
$response = curl_exec($ch);

// 6. AMBIL INFO PENTING UNTUK DEBUG
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// 7. Tutup cURL
curl_close($ch);

// 8. Tampilkan hasil debug
echo "-----------------------------------<br>";
echo "<b>HTTP Status Code:</b> " . $httpCode . "<br>";
echo "<b>cURL Error (jika ada):</b> " . $curlError . "<br>";
echo "<b>Respon Mentah dari Server:</b><br>";
echo $response;
echo "<br>-----------------------------------<br>";

// 9. Coba decode JSON dan tampilkan sebagai array
echo "<h3>Respon dalam format Array (json_decode):</h3>";
$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";
?>