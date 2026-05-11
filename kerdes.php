<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$kerdes_id = $_GET['id'];

$stmt = $kapcsolat->prepare("
    SELECT k.*, f.felhasznalonev 
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

$stmt = $kapcsolat->prepare("
    SELECT h.*, f.felhasznalonev 
    FROM hozzaszolasok h 
    JOIN felhasznalok f ON h.felhasznalo_id = f.id 
    WHERE h.kerdes_id = ? 
    ORDER BY h.letrehozva ASC
");
$stmt->bind_param("i", $kerdes_id);
$stmt->execute();
$hozzaszolasok = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($kerdes['cim']) ?> - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav>
        <a href="index.php">Főoldal</a>
        <?php if ($bejelentkezett): ?>
            <a href="kerdesFelvetel.php">Kérdés feltevése</a>
            <a href="profil.php">Profilom</a>
            <a href="beallitasok.php">Beállítások</a>
            <a href="kilepes.php">Kilépés</a>
        <?php else: ?>
            <a href="bejelentkezes.php">Bejelentkezés</a>
            <a href="regisztralas.php">Regisztráció</a>
            <a href="beallitasok.php">Beállítások</a>
        <?php endif; ?>
    </nav>
    <main>
        <div class="kerdes-reszlet">
            <div class="kerdes-fejlec">
                <h1><?= htmlspecialchars($kerdes['cim']) ?></h1>
                <?php if ($bejelentkezett && $bejelentkezett['id'] == $kerdes['felhasznalo_id']): ?>
                    <a href="kerdesTorles.php?id=<?= $kerdes['id'] ?>" class="torles-gomb" onclick="return confirm('Biztosan törlöd ezt a kérdést?')">Törlés</a>
                <?php endif; ?>
            </div>
            <div class="kerdes-tartalom">
                <p><?= nl2br(htmlspecialchars($kerdes['tartalom'])) ?></p>
                <small>Kérdezte: <?= htmlspecialchars($kerdes['felhasznalonev']) ?> - <?= date("Y-m-d H:i", strtotime($kerdes['letrehozva'])) ?></small>
            </div>
        </div>

        <div class="hozzaszolasok">
            <h2>Válaszok (<?= count($hozzaszolasok) ?>)</h2>
            <?php if (count($hozzaszolasok) > 0): ?>
                <?php foreach ($hozzaszolasok as $hozzaszolas): ?>
                    <div class="kartya">
                        <p><?= nl2br(htmlspecialchars($hozzaszolas['tartalom'])) ?></p>
                        <small>Írta: <?= htmlspecialchars($hozzaszolas['felhasznalonev']) ?> - <?= date("Y-m-d H:i", strtotime($hozzaszolas['letrehozva'])) ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Még nincs válasz. Légy az első!</p>
            <?php endif; ?>
        </div>

        <?php if ($bejelentkezett): ?>
            <div class="uj-hozzaszolas">
                <h3>Írj egy választ</h3>
                <form method="POST" action="hozzaszolasMentes.php">
                    <input type="hidden" name="kerdes_id" value="<?= $kerdes_id ?>">
                    <textarea name="tartalom" rows="4" placeholder="Írd ide a válaszod..." required></textarea>
                    <button type="submit">Válasz küldése</button>
                </form>
            </div>
        <?php else: ?>
            <p><a href="bejelentkezes.php">Jelentkezz be</a> a válaszíráshoz!</p>
        <?php endif; ?>
    </main>

    <script>
        const darkMode = localStorage.getItem('dark_mode');
        if (darkMode === 'dark') {
            document.body.classList.add('dark');
            document.body.classList.remove('light');
        } else {
            document.body.classList.add('light');
            document.body.classList.remove('dark');
        }
    </script>
</body>
</html>