<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

$eredmeny = $kapcsolat->query("
    SELECT k.*, f.felhasznalonev 
    FROM kerdesek k 
    JOIN felhasznalok f ON k.felhasznalo_id = f.id 
    ORDER BY k.letrehozva DESC
");
$kerdesek = $eredmeny->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GyakoriKérdések</title>
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
        <h1>Kérdések</h1>
        <?php if (count($kerdesek) > 0): ?>
            <?php foreach ($kerdesek as $kerdes): ?>
                <div class="kartya">
                    <a href="kerdes.php?id=<?= $kerdes['id'] ?>" class="kerdes-cim">
                        <h3><?= htmlspecialchars($kerdes['cim']) ?></h3>
                    </a>
                    <p><?= nl2br(htmlspecialchars(substr($kerdes['tartalom'], 0, 200))) ?>...</p>
                    <small>Kérdezte: <?= htmlspecialchars($kerdes['felhasznalonev']) ?> - <?= date("Y-m-d H:i", strtotime($kerdes['letrehozva'])) ?></small>
                    <?php if ($bejelentkezett && $bejelentkezett['id'] == $kerdes['felhasznalo_id']): ?>
                        <div class="gombok">
                            <a href="kerdesTorles.php?id=<?= $kerdes['id'] ?>" class="torles-gomb" onclick="return confirm('Biztosan törlöd ezt a kérdést?')">Törlés</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Még nincs kérdés. Légy az első!</p>
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