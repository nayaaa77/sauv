<?php
// includes/rajaongkir_config.php

// 1. Ganti 'MASUKKAN_API_KEY_ANDA_DISINI' 
//    dengan API Key dari dashboard RajaOngkir Anda.
define('RAJAONGKIR_API_KEY', 'MASUKKAN_API_KEY_ANDA_DISINI');

// 2. Biarkan 'starter' jika Anda menggunakan akun gratis.
define('RAJAONGKIR_ACCOUNT_TYPE', 'starter'); 
    
// 3. Tentukan ID Kota Asal Pengiriman (Toko Anda).
//    Anda bisa mendapatkan ID kota ini dari dashboard RajaOngkir.
//    Contoh: '152' adalah ID untuk 'Kota Jakarta Pusat'. Ganti dengan ID kota Anda.
define('RAJAONGKIR_ORIGIN_CITY_ID', '152'); 

?>