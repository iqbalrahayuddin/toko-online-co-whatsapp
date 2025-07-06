<?php
require_once "config.php";

header('Content-Type: application/json');

// Ambil aksi dari parameter GET
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Cek apakah RapidAPI Key sudah diatur
if (empty($storeConfig['rapidApiKey'])) {
    echo json_encode(['success' => false, 'message' => 'RapidAPI Key tidak diatur di Admin Panel.']);
    exit;
}

// Fungsi untuk melakukan request cURL ke RapidAPI
function callRapidAPI($url, $apiKey) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    $headers = [
        'X-RapidAPI-Host: cek-resi-cek-ongkir.p.rapidapi.com',
        'X-RapidAPI-Key: ' . $apiKey
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        return json_encode(['success' => false, 'message' => 'Curl Error: ' . $error_msg]);
    }
    curl_close($ch);
    return $result;
}

switch ($action) {
    case 'autocomplete':
        $query = isset($_GET['q']) ? urlencode($_GET['q']) : '';
        if (empty($query)) {
            echo json_encode(['success' => false, 'message' => 'Query pencarian kosong.']);
            exit;
        }
        $apiUrl = "https://cek-resi-cek-ongkir.p.rapidapi.com/general/autocomplete?q=" . $query;
        echo callRapidAPI($apiUrl, $storeConfig['rapidApiKey']);
        break;

    case 'shipping_cost':
        $originId = $storeConfig['originAreaId'];
        $destinationId = isset($_GET['destination']) ? $_GET['destination'] : '';
        $weight = isset($_GET['weight']) ? $_GET['weight'] : '1000'; // Default 1kg jika tidak ada

        if (empty($destinationId)) {
            echo json_encode(['success' => false, 'message' => 'ID area tujuan tidak valid.']);
            exit;
        }
        
        $apiUrl = "https://cek-resi-cek-ongkir.p.rapidapi.com/shipping-cost?originAreaId={$originId}&destinationAreaId={$destinationId}&weight={$weight}";
        echo callRapidAPI($apiUrl, $storeConfig['rapidApiKey']);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak valid.']);
        break;
}
?>
