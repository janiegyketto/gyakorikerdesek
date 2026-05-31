<?php
require_once 'beallitas.php';
$bejelentkezett = getBejelentkezettFelhasznalo($kapcsolat);

if (!$bejelentkezett || ($bejelentkezett['szerepkor'] !== 'admin' && $bejelentkezett['szerepkor'] !== 'moderator')) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $bejelentkezett['szerepkor'] === 'admin') {
    if (isset($_POST['szerep_modositas'])) {
        $f_id = $_POST['felhasznalo_id'];
        $uj_szerep = $_POST['szerepkor'];
        if (in_array($uj_szerep, ['user', 'moderator', 'admin'])) {
            $stmt = $kapcsolat->prepare("UPDATE felhasznalok SET szerepkor = ? WHERE id = ?");
            $stmt->bind_param("si", $uj_szerep, $f_id);
            $stmt->execute();
        }
    }
    
    if (isset($_POST['felhasznalo_torles'])) {
        $f_id = $_POST['felhasznalo_id'];
        if ($f_id != $bejelentkezett['id']) {
            $stmt = $kapcsolat->prepare("DELETE FROM felhasznalok WHERE id = ?");
            $stmt->bind_param("i", $f_id);
            $stmt->execute();
        }
    }
    header("Location: admin.php");
    exit;
}

$eredmeny = $kapcsolat->query("SELECT id, felhasznalonev, email, szerepkor FROM felhasznalok ORDER BY id ASC");
$felhasznalok = $eredmeny->fetch_all(MYSQLI_ASSOC);

$log_eredmeny = $kapcsolat->query("SELECT id, adatok, idopont FROM ai_logs WHERE esemeny_tipus = 'torolt_kerdes' ORDER BY idopont DESC");
$torolt_kerdesek = $log_eredmeny->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin felület - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 40px; }
        th, td { border: 1px solid #777; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; color: #000; }
        .dark th { background-color: #333; color: #fff; }
    </style>
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
    <nav style="margin-top: 5px; background-color: #444;">
        <?php if ($bejelentkezett['szerepkor'] === 'admin' || $bejelentkezett['szerepkor'] === 'moderator'): ?>
            <a href="admin.php">Felhasználók</a>
        <?php endif; ?>
    </nav>
    <main>
        <h1>Adminisztrációs Vezérlőpult</h1>
        <p>Bejelentkezve mint: <strong><?= htmlspecialchars($bejelentkezett['felhasznalonev']) ?></strong> (<?= htmlspecialchars($bejelentkezett['szerepkor']) ?>)</p>
        
        <h2>Felhasználók kezelése</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Felhasználónév</th>
                    <th>Email</th>
                    <th>Szerepkör</th>
                    <th>Műveletek</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($felhasznalok as $f): ?>
                    <tr>
                        <td><?= $f['id'] ?></td>
                        <td><?= htmlspecialchars($f['felhasznalonev']) ?></td>
                        <td><?= htmlspecialchars($f['email']) ?></td>
                        <td>
                            <?php if ($bejelentkezett['szerepkor'] === 'admin'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="felhasznalo_id" value="<?= $f['id'] ?>">
                                    <select name="szerepkor" onchange="this.form.submit()">
                                        <option value="user" <?= $f['szerepkor'] === 'user' ? 'selected' : '' ?>>User</option>
                                        <option value="moderator" <?= $f['szerepkor'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                                        <option value="admin" <?= $f['szerepkor'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <input type="hidden" name="szerep_modositas" value="1">
                                </form>
                            <?php else: ?>
                                <?= htmlspecialchars($f['szerepkor']) ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($bejelentkezett['szerepkor'] === 'admin' && $f['id'] != $bejelentkezett['id']): ?>
                                <form method="POST" style="display:inline;" onclick="return confirm('Biztosan törlöd ezt a felhasználót?')">
                                    <input type="hidden" name="felhasznalo_id" value="<?= $f['id'] ?>">
                                    <button type="submit" name="felhasznalo_torles" class="torles-gomb" style="border:none; cursor:pointer;">Törlés</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>AI Log - Törölt kérdések archívuma</h2>
        <?php if (count($torolt_kerdesek) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kérdés címe</th>
                        <th>Eredeti szerző</th>
                        <th>Törlés időpontja</th>
                        <th>Művelet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($torolt_kerdesek as $sor): ?>
                        <?php $adat = json_decode($sor['adatok'], true); ?>
                        <tr>
                            <td><?= $sor['id'] ?></td>
                            <td><?= htmlspecialchars($adat['cim'] ?? 'Nincs cím') ?></td>
                            <td><?= htmlspecialchars($adat['kerdezo'] ?? 'Ismeretlen') ?></td>
                            <td><?= $sor['idopont'] ?></td>
                            <td>
                                <a href="adminKerdesMegtekintes.php?id=<?= $sor['id'] ?>" class="torles-gomb" style="background-color: #008cba; text-decoration: none; padding: 5px 10px; display: inline-block; color: white; border-radius: 4px;">Megnyitás</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nincsenek törölt kérdések az AI naplóban.</p>
        <?php endif; ?>
    </main>
    <script>
        if (localStorage.getItem('dark_mode') === 'dark') document.body.classList.add('dark');
    </script>
</body>
</html>