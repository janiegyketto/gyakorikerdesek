<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!$bejelentkezett) {
    header("Location: bejelentkezes.php");
    exit;
}

$uzenet = "";
$hiba = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['profil_modositas'])) {
    $uj_email = trim($_POST['email']);
    $uj_jelszo = $_POST['jelszo'];

    if (empty($uj_email)) {
        $hiba = "Az email mező nem lehet üres!";
    } else {
        $stmt = $kapcsolat->prepare("SELECT id FROM felhasznalok WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $uj_email, $bejelentkezett['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $hiba = "Ez az email cím már használatban van!";
        } else {
            if (!empty($uj_jelszo)) {
                $hash_jelszo = password_hash($uj_jelszo, PASSWORD_BCRYPT);
                $stmt = $kapcsolat->prepare("UPDATE felhasznalok SET email = ?, jelszo = ? WHERE id = ?");
                $stmt->bind_param("ssi", $uj_email, $hash_jelszo, $bejelentkezett['id']);
            } else {
                $stmt = $kapcsolat->prepare("UPDATE felhasznalok SET email = ? WHERE id = ?");
                $stmt->bind_param("si", $uj_email, $bejelentkezett['id']);
            }

            if ($stmt->execute()) {
                $uzenet = "Adatok sikeresen frissítve!";
                $bejelentkezett['email'] = $uj_email;
            } else {
                $hiba = "Hiba történt a mentés során!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beállítások - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav>
        <a href="index.php">Főoldal</a>
        <?php if ($bejelentkezett): ?>
            <a href="kerdesFelvetel.php">Kérdés feltevése</a>
            <a href="profil.php">Profilom</a>
            <a href="beallitasok.php">Beállítások</a>
            <?php if ($bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator'): ?>
                <a href="admin.php">Admin panel</a>
            <?php endif; ?>
            <a href="kilepes.php">Kilépés</a>
        <?php else: ?>
            <a href="bejelentkezes.php">Bejelentkezés</a>
            <a href="regisztralas.php">Regisztráció</a>
            <a href="beallitasok.php">Beállítások</a>
        <?php endif; ?>
    </nav>
    <main>
        <h1>Beállítások</h1>
        
        <?php if (!empty($uzenet)) echo "<p style='color:green'>$uzenet</p>"; ?>
        <?php if (!empty($hiba)) echo "<p style='color:red'>$hiba</p>"; ?>

        <h2>Profil adatok módosítása</h2>
        <form method="POST" style="margin-bottom: 30px;">
            <input type="hidden" name="profil_modositas" value="1">
            <div class="beallitas-mezo">
                <label>Email cím:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($bejelentkezett['email']) ?>" required>
            </div>
            <div class="beallitas-mezo">
                <label>Új jelszó (üresen hagyható):</label>
                <input type="password" name="jelszo" placeholder="Csak ha változtatni akarod">
            </div>
            <button type="submit">Adatok mentése</button>
        </form>

        <hr>

        <h2>Megjelenítés</h2>
        <div class="beallitas-mezo">
            <label>Megjelenés módja:</label>
            <select id="darkModeSelect">
                <option value="light">Világos</option>
                <option value="dark">Sötét</option>
            </select>
        </div>
        <button id="mentesGomb">Mód mentése</button>
        <div id="uzenet" class="uzenet"></div>
    </main>

    <script>
        const select = document.getElementById('darkModeSelect');
        const mentesGomb = document.getElementById('mentesGomb');
        const uzenetDiv = document.getElementById('uzenet');
        
        const betoltottMod = localStorage.getItem('dark_mode');
        if (betoltottMod === 'dark') {
            select.value = 'dark';
            document.body.classList.add('dark');
            document.body.classList.remove('light');
        } else {
            select.value = 'light';
            document.body.classList.add('light');
            document.body.classList.remove('dark');
        }
        
        mentesGomb.addEventListener('click', function() {
            const valasztott = select.value;
            localStorage.setItem('dark_mode', valasztott);
            
            if (valasztott === 'dark') {
                document.body.classList.add('dark');
                document.body.classList.remove('light');
            } else {
                document.body.classList.add('light');
                document.body.classList.remove('dark');
            }
            
            uzenetDiv.textContent = 'Téma beállítása mentve!';
            uzenetDiv.style.color = 'green';
            
            setTimeout(() => {
                uzenetDiv.textContent = '';
            }, 2000);
        });
    </script>
</body>
</html>