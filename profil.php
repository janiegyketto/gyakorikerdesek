<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);
if (!$bejelentkezett) {
    header("Location: bejelentkezes.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilom - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav>
        <a href="index.php">Főoldal</a>
        <a href="kerdesFelvetel.php">Kérdés feltevése</a>
        <a href="profil.php">Profilom</a>
        <a href="beallitasok.php">Beállítások</a>
        <a href="kilepes.php">Kilépés</a>
    </nav>
    <main>
        <h1>Profilom</h1>
        <div class="kartya">
            <p><strong>Felhasználónév:</strong> <?= htmlspecialchars($bejelentkezett['felhasznalonev']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($bejelentkezett['email']) ?></p>
            <p><strong>Regisztráció:</strong> <?= date("Y-m-d H:i", strtotime($bejelentkezett['regisztralva'])) ?></p>
        </div>
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