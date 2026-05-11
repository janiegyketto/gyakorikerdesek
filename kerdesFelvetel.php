<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);
if (!$bejelentkezett) {
    header("Location: bejelentkezes.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cim = $_POST['cim'];
    $tartalom = $_POST['tartalom'];
    
    if (!empty($cim) && !empty($tartalom)) {
        $stmt = $kapcsolat->prepare("INSERT INTO kerdesek (cim, tartalom, felhasznalo_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $cim, $tartalom, $bejelentkezett['id']);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $hiba = "Cím és tartalom kitöltése kötelező!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új kérdés - GyakoriKérdések</title>
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
        <h1>Új kérdés feltevése</h1>
        <?php if (isset($hiba)) echo "<p style='color:red'>$hiba</p>"; ?>
        <form method="POST">
            <input type="text" name="cim" placeholder="Kérdés címe" required>
            <textarea name="tartalom" placeholder="Kérdés részletesen..." rows="5" required></textarea>
            <button type="submit">Kérdés feltevése</button>
        </form>
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