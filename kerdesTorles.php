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

$stmt = $kapcsolat->prepare("
    SELECT k.felhasznalo_id, k.cim, k.tartalom, f.felhasznalonev 
    FROM kerdesek k 
    JOIN felhasznalok f ON k.felhasznalo_id = f.id 
    WHERE k.id = ?
");
$stmt->bind_param("i", $kerdes_id);
$stmt->execute();
$kerdes = $stmt->get_result()->fetch_assoc();

if (!$kerdes) {
    header("Location: index.php");
    exit;
}

if ($kerdes['felhasznalo_id'] != $bejelentkezett['id'] && $bejelentkezett['szerepkor'] !== 'admin' && $bejelentkezett['szerepkor'] !== 'moderator') {
    header("Location: index.php");
    exit;
}

$vStmt = $kapcsolat->prepare("
    SELECT h.tartalom, h.letrehozva, f.felhasznalonev 
    FROM hozzaszolasok h 
    JOIN felhasznalok f ON h.felhasznalo_id = f.id 
    WHERE h.kerdes_id = ? 
    ORDER BY h.letrehozva ASC
");
$vStmt->bind_param("i", $kerdes_id);
$vStmt->execute();
$vEredmeny = $vStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$valaszok_tomb = [];
foreach ($vEredmeny as $vSor) {
    $valaszok_tomb[] = [
        'tartalom' => $vSor['tartalom'],
        'letrehozva' => $vSor['letrehozva'],
        'valaszolo' => $vSor['felhasznalonev']
    ];
}

$log_adat = [
    'kerdes_id' => $kerdes_id,
    'cim' => $kerdes['cim'],
    'tartalom' => $kerdes['tartalom'],
    'kerdezo' => $kerdes['felhasznalonev'],
    'valaszok' => $valaszok_tomb
];

if (function_exists('logAIEsemeny')) {
    logAIEsemeny($kapcsolat, 'torolt_kerdes', $log_adat);
}

$stmt = $kapcsolat->prepare("DELETE FROM kerdesek WHERE id = ?");
$stmt->bind_param("i", $kerdes_id);
$stmt->execute();

header("Location: index.php");
exit;
?>