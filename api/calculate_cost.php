<?php
require_once('../config.php');
header('Content-Type: application/json');

// Ambil data dari AJAX checkout.js
$destination = $_POST['destination_id'] ?? 0;
$weight = $_POST['weight'] ?? 0;
$courier = $_POST['courier'] ?? '';

if ($destination == 0 || $weight == 0 || $courier == '') {
    echo json_encode(['error' => 'Data tujuan, berat, atau kurir tidak lengkap.']);
    exit;
}

// Data yang akan dikirim ke API (menggunakan ID Asal dari config.php)
$data_to_send = [
    'origin'      => ORIGIN_DISTRICT_ID, 
    'destination' => $destination,
    'weight'      => $weight,
    'courier'     => $courier
];

$url = API_URL . '/calculate/district/domestic-cost';
$apiKey = RAJAONGKIR_API_KEY;

// KODE cURL POST
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data_to_send)); 

$headers = [
    'key: ' . $apiKey,
    'Content-Type: application/x-www-form-urlencoded' // Kunci sukses!
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

// Kirim balik respon mentah dari API ke JavaScript
echo $response; 
?>