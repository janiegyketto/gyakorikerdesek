<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

header('Content-Type: application/json');

if (!$bejelentkezett) {
    echo json_encode(['success' => false, 'error' => 'Nincs bejelentkezve']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tartalom = $_POST['tartalom'] ?? '';
    $kerdes_id = $_POST['kerdes_id'] ?? 0;
    
    if (empty($tartalom)) {
        echo json_encode(['success' => false, 'error' => 'A válasz nem lehet üres']);
        exit;
    }
    
    $stmt = $kapcsolat->prepare("INSERT INTO hozzaszolasok (tartalom, felhasznalo_id, kerdes_id) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $tartalom, $bejelentkezett['id'], $kerdes_id);
    
    if ($stmt->execute()) {
        $ujId = $kapcsolat->insert_id;
        $datum = date("Y-m-d H:i:s");
        
        echo json_encode([
            'success' => true, 
            'id' => $ujId,
            'felhasznalonev' => $bejelentkezett['felhasznalonev'],
            'tartalom' => nl2br(htmlspecialchars($tartalom)),
            'letrehozva' => date("Y-m-d H:i", strtotime($datum))
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Adatbázis hiba']);
    }
    exit;
}
?>