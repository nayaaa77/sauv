<?php
echo "<h1>Tes API Komerce (HITUNG ONGKIR - v3 Sesuai Docs)</h1>";

// 1. Masukkan API Key "Shipping Cost" Anda
$apiKey = 'SrZPSdpo43fc1282affbab5brmYUADkf'; 

// 2. URL Endpoint untuk hitung ongkir
$url = 'https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost';

// 3. Tentukan DATA yang akan dikirim (Gunakan huruf kecil sesuai docs)
$data_to_send = [
    'origin'      => 790, // ID Gunung Putri
    'destination' => 424, // ID CIBIRU
    'weight'      => 1000, // Berat
    'courier'     => 'jne'
];

// ------------------------------------------------------------------
// PERUBAHAN KODE DIMULAI DI SINI
// ------------------------------------------------------------------

// 4. Siapkan header (KITA TAMBAHKAN KEMBALI CONTENT-TYPE)
$headers = [
    'key: ' . $apiKey,
    'Content-Type: application/x-www-form-urlencoded' // INI KUNCINYA
];

// 5. Inisialisasi cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

// 6. Set cURL sebagai request POST
curl_setopt($ch, CURLOPT_POST, true);

// 7. Kirim data sebagai FORM (ini sudah benar dari v2)
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_to_send)); 

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Set header

// ------------------------------------------------------------------
// KODE LANJUT SEPERTI BIASA
// ------------------------------------------------------------------

// 8. Eksekusi cURL
$response = curl_exec($ch);

// 9. AMBIL INFO PENTING UNTUK DEBUG
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

// 10. Tutup cURL
curl_close($ch);

// 11. Tampilkan hasil debug
echo "-----------------------------------<br>";
echo "Mengirim data ke: " . $url . "<br>";
echo "Data yang dikirim: " . http_build_query($data_to_send) . "<br><br>";
echo "<b>HTTP Status Code:</b> " . $httpCode . "<br>";
echo "<b>cURL Error (jika ada):</b> " . $curlError . "<br>";
echo "<b>Respon Mentah dari Server:</b><br>";
echo $response;
echo "<br>-----------------------------------<br>";

// 12. Coba decode JSON dan tampilkan sebagai array
echo "<h3>Respon dalam format Array (json_decode):</h3>";
$data = json_decode($response, true);

echo "<pre>";
print_r($data);
echo "</pre>";
?>