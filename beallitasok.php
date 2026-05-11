<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);
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
            <a href="kilepes.php">Kilépés</a>
        <?php else: ?>
            <a href="bejelentkezes.php">Bejelentkezés</a>
            <a href="regisztralas.php">Regisztráció</a>
            <a href="beallitasok.php">Beállítások</a>
        <?php endif; ?>
    </nav>
    <main>
        <h1>Beállítások</h1>
        <div class="beallitas-mezo">
            <label>Megjelenés módja:</label>
            <select id="darkModeSelect">
                <option value="light">Világos</option>
                <option value="dark">Sötét</option>
            </select>
        </div>
        <button id="mentesGomb">Mentés</button>
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
            
            uzenetDiv.textContent = 'Beállítás mentve!';
            uzenetDiv.style.color = 'green';
            
            setTimeout(() => {
                uzenetDiv.textContent = '';
            }, 2000);
        });
    </script>
</body>
</html>