<?php
session_start();
$kapcsolat = new mysqli("localhost", "root", "", "gyakorikerdesek");
if ($kapcsolat->connect_error) {
    die("Adatbázis hiba: " . $kapcsolat->connect_error);
}

function getBejelentkezettFelhasznalo($kapcsolat) {
    if (!isset($_SESSION['felhasznalo_id'])) return null;
    $stmt = $kapcsolat->prepare("SELECT id, felhasznalonev, email, szerepkor, regisztralva FROM felhasznalok WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['felhasznalo_id']);
    $stmt->execute();
    $eredmeny = $stmt->get_result();
    return $eredmeny->fetch_assoc();
}

function logAIEsemeny($kapcsolat, $esemeny_tipus, $adatok_tomb) {
    $felhasznalo_id = isset($_SESSION['felhasznalo_id']) ? $_SESSION['felhasznalo_id'] : null;
    $json_adatok = json_encode($adatok_tomb, JSON_UNESCAPED_UNICODE);
    $stmt = $kapcsolat->prepare("INSERT INTO ai_logs (felhasznalo_id, esemeny_tipus, adatok) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $felhasznalo_id, $esemeny_tipus, $json_adatok);
    $stmt->execute();
}
?>