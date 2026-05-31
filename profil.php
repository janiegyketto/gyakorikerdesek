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
        <?php if ($bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator'): ?>
            <a href="admin.php">Admin panel</a>
        <?php endif; ?>
        <a href="kilepes.php">Kilépés</a>
    </nav>
    <main>
        <h1>Profilom</h1>
        <div class="kartya">
            <p><strong>Felhasználónév:</strong> <?= htmlspecialchars($bejelentkezett['felhasznalonev']) ?></p>
            <p><strong>Email cím:</strong> <?= htmlspecialchars($bejelentkezett['email']) ?></p>
            <p><strong>Jogosultsági szint:</strong> <?= htmlspecialchars($bejelentkezett['szerepkor']) ?></p>
            <p><strong>Regisztráció dátuma:</strong> <?= htmlspecialchars($bejelentkezett['regisztralva']) ?></p>
        </div>
    </main>
    <script>
        if (localStorage.getItem('dark_mode') === 'dark') document.body.classList.add('dark');
    </script>
</body>
</html>