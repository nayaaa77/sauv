<?php
require_once('../config.php');

header('Content-Type: application/json');

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? 0;
$result = [];

// FUNGSI UTAMA UNTUK MENGAMBIL DATA DARI API KOMERCE
function fetch_komerce_data($endpoint) {
    $url = API_URL . $endpoint;
    $headers = ['key: ' . RAJAONGKIR_API_KEY];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        return json_decode($response, true)['data'];
    }
    return ['error' => 'Gagal mengambil data lokasi. Code: ' . $httpCode];
}

switch ($type) {
    case 'province':
        $result = fetch_komerce_data('/destination/province');
        break;
    case 'city':
        if ($id > 0) {
            $result = fetch_komerce_data("/destination/city/{$id}");
        }
        break;
    case 'district':
        if ($id > 0) {
            $result = fetch_komerce_data("/destination/district/{$id}");
        }
        break;
}

echo json_encode($result);
?>