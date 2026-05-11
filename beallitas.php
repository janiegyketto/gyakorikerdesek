<?php
session_start();
$kapcsolat = new mysqli("localhost", "root", "", "gyakorikerdesek");
if ($kapcsolat->connect_error) {
    die("Adatbázis hiba: " . $kapcsolat->connect_error);
}

function getBejelentkezettFelhasznalo($kapcsolat) {
    if (!isset($_SESSION['felhasznalo_id'])) return null;
    $stmt = $kapcsolat->prepare("SELECT id, felhasznalonev, email, regisztralva FROM felhasznalok WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['felhasznalo_id']);
    $stmt->execute();
    $eredmeny = $stmt->get_result();
    return $eredmeny->fetch_assoc();
}
?>