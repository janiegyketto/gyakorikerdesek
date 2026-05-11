<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!$bejelentkezett) {
    header("Location: bejelentkezes.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tartalom = $_POST['tartalom'];
    $kerdes_id = $_POST['kerdes_id'];
    
    if (!empty($tartalom)) {
        $stmt = $kapcsolat->prepare("INSERT INTO hozzaszolasok (tartalom, felhasznalo_id, kerdes_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $tartalom, $bejelentkezett['id'], $kerdes_id);
        $stmt->execute();
    }
    
    header("Location: kerdes.php?id=" . $kerdes_id);
    exit;
}
?>