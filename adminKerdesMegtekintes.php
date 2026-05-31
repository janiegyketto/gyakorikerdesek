<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!$bejelentkezett || ($bejelentkezett['szerepkor'] !== 'admin' && $bejelentkezett['szerepkor'] !== 'moderator')) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$log_id = $_GET['id'];
$stmt = $kapcsolat->prepare("SELECT adatok, idopont FROM ai_logs WHERE id = ? AND esemeny_tipus = 'torolt_kerdes'");
$stmt->bind_param("i", $log_id);
$stmt->execute();
$eredmeny = $stmt->get_result()->fetch_assoc();

if (!$eredmeny) {
    header("Location: admin.php");
    exit;
}

$adat = json_decode($eredmeny['adatok'], true);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Törölt kérdés megtekintése</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav>
        <a href="index.php">Főoldal</a>
        <a href="kerdesFelvetel.php">Kérdés feltevése</a>
        <a href="profil.php">Profilom</a>
        <a href="beallitasok.php">Beállítások</a>
        <a href="admin.php">Admin panel</a>
        <a href="kilepes.php">Kilépés</a>
    </nav>
    <main>
        <div class="gombok" style="margin-bottom: 20px;">
            <a href="admin.php" class="torles-gomb" style="background-color: #555; text-decoration: none; padding: 8px 15px; display: inline-block; color: white; border-radius: 4px;">Vissza az admin panelre</a>
        </div>

        <div class="kartya" style="border-left: 5px solid #d9534f;">
            <h2>[TÖRÖLT] <?= htmlspecialchars($adat['cim'] ?? 'Nincs cím') ?></h2>
            <p><?= nl2br(htmlspecialchars($adat['tartalom'] ?? 'Nincs tartalom')) ?></p>
            <hr>
            <small>Eredeti kérdező: <strong><?= htmlspecialchars($adat['kerdezo'] ?? 'Ismeretlen') ?></strong></small><br>
            <small>Törlés időpontja: <strong><?= $eredmeny['idopont'] ?></strong></small>
        </div>

        <h3>Eredeti válaszok (<?= count($adat['valaszok'] ?? []) ?>)</h3>
        <?php if (!empty($adat['valaszok'])): ?>
            <?php foreach ($adat['valaszok'] as $v): ?>
                <div class="kartya">
                    <p><?= nl2br(htmlspecialchars($v['tartalom'] ?? '')) ?></p>
                    <small>Írta: <strong><?= htmlspecialchars($v['valaszolo'] ?? 'Ismeretlen') ?></strong> - <?= htmlspecialchars($v['letrehozva'] ?? '') ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Ehhez a kérdéshez nem tartoztak válaszok a törlés pillanatában.</p>
        <?php endif; ?>
    </main>
    <script>
        if (localStorage.getItem('dark_mode') === 'dark') document.body.classList.add('dark');
    </script>
</body>
</html>