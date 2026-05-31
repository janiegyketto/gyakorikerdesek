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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hozzaszolas_torles'])) {
    $h_id = $_POST['hozzaszolas_id'];
    if ($bejelentkezett && ($bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator')) {
        $delStmt = $kapcsolat->prepare("DELETE FROM hozzaszolasok WHERE id = ?");
        $delStmt->bind_param("i", $h_id);
        $delStmt->execute();
        header("Location: kerdes.php?id=" . $kerdes_id);
        exit;
    }
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
        <div class="kerdes-reszlet">
            <div class="kerdes-fejlec">
                <h1><?= htmlspecialchars($kerdes['cim']) ?></h1>
                <?php if ($bejelentkezett && ($bejelentkezett['id'] == $kerdes['felhasznalo_id'] || $bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator')): ?>
                    <a href="kerdesTorles.php?id=<?= $kerdes['id'] ?>" class="torles-gomb" onclick="return confirm('Biztosan törlöd ezt a kérdést?')">Törlés</a>
                <?php endif; ?>
            </div>
            <div class="kerdes-tartalom">
                <p><?= nl2br(htmlspecialchars($kerdes['tartalom'])) ?></p>
                <small>Kérdezte: <?= htmlspecialchars($kerdes['felhasznalonev']) ?> - <?= date("Y-m-d H:i", strtotime($kerdes['letrehozva'])) ?></small>
            </div>
        </div>

        <div class="hozzaszolasok">
            <h2>Válaszok (<span id="valaszokSzama"><?= count($hozzaszolasok) ?></span>)</h2>
            <div id="valaszokListaja">
                <?php if (count($hozzaszolasok) > 0): ?>
                    <?php foreach ($hozzaszolasok as $hozzaszolas): ?>
                        <div class="kartya" id="valasz-<?= $hozzaszolas['id'] ?>">
                            <p><?= nl2br(htmlspecialchars($hozzaszolas['tartalom'])) ?></p>
                            <small>Írta: <?= htmlspecialchars($hozzaszolas['felhasznalonev']) ?> - <?= date("Y-m-d H:i", strtotime($hozzaszolas['letrehozva'])) ?></small>
                            <?php if ($bejelentkezett && ($bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator')): ?>
                                <form method="POST" style="margin-top:5px; display:inline;">
                                    <input type="hidden" name="hozzaszolas_id" value="<?= $hozzaszolas['id'] ?>">
                                    <button type="submit" name="hozzaszolas_torles" class="torles-gomb" style="border:none; cursor:pointer;" onclick="return confirm('Törlöd ezt a hozzászólást?')">Moderálás (Törlés)</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p id="nincsValaszUzenet">Még nincs válasz. Légy az első!</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($bejelentkezett): ?>
            <div class="uj-hozzaszolas">
                <h3>Írj egy választ</h3>
                <form id="valaszForm">
                    <input type="hidden" name="kerdes_id" id="kerdes_id" value="<?= $kerdes_id ?>">
                    <textarea name="tartalom" id="valaszTartalom" rows="4" placeholder="Írd ide a válaszod..." required></textarea>
                    <div id="valaszHiba" class="hiba"></div>
                    <button type="submit" id="kuldesGomb">Válasz küldése</button>
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
        
        <?php if ($bejelentkezett): ?>
        const valaszForm = document.getElementById('valaszForm');
        const valaszTartalom = document.getElementById('valaszTartalom');
        const valaszHiba = document.getElementById('valaszHiba');
        const kuldesGomb = document.getElementById('kuldesGomb');
        const valaszokListaja = document.getElementById('valaszokListaja');
        const valaszokSzama = document.getElementById('valaszokSzama');
        
        valaszForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const tartalom = valaszTartalom.value.trim();
            if (tartalom === '') {
                valaszHiba.textContent = 'A válasz nem lehet üres!';
                return;
            }
            
            valaszHiba.textContent = '';
            kuldesGomb.disabled = true;
            kuldesGomb.textContent = 'Küldés...';
            
            const formData = new FormData();
            formData.append('tartalom', tartalom);
            formData.append('kerdes_id', document.getElementById('kerdes_id').value);
            
            fetch('hozzaszolasMentes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const nincsValasz = document.getElementById('nincsValaszUzenet');
                    if (nincsValasz) {
                        nincsValasz.remove();
                    }
                    
                    const ujValasz = document.createElement('div');
                    ujValasz.className = 'kartya';
                    ujValasz.id = 'valasz-' + data.id;
                    ujValasz.innerHTML = `
                        <p>${data.tartalom}</p>
                        <small>Írta: ${data.felhasznalonev} - ${data.letrehozva}</small>
                    `;
                    valaszokListaja.appendChild(ujValasz);
                    
                    const jelenlegiSzam = parseInt(valaszokSzama.textContent);
                    valaszokSzama.textContent = jelenlegiSzam + 1;
                    
                    valaszTartalom.value = '';
                } else {
                    valaszHiba.textContent = data.error;
                }
            })
            .catch(error => {
                valaszHiba.textContent = 'Hiba történt a küldés során!';
            })
            .finally(() => {
                kuldesGomb.disabled = false;
                kuldesGomb.textContent = 'Válasz küldése';
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>