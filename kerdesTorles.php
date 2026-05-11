<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!$bejelentkezett) {
    header("Location: bejelentkezes.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$kerdes_id = $_GET['id'];

$stmt = $kapcsolat->prepare("SELECT felhasznalo_id FROM kerdesek WHERE id = ?");
$stmt->bind_param("i", $kerdes_id);
$stmt->execute();
$eredmeny = $stmt->get_result();
$kerdes = $eredmeny->fetch_assoc();

if (!$kerdes) {
    header("Location: index.php");
    exit;
}

if ($kerdes['felhasznalo_id'] != $bejelentkezett['id']) {
    header("Location: index.php");
    exit;
}

$stmt = $kapcsolat->prepare("DELETE FROM kerdesek WHERE id = ?");
$stmt->bind_param("i", $kerdes_id);
$stmt->execute();

header("Location: index.php");
exit;
?>