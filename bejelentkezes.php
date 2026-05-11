<?php
require_once 'beallitas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $jelszo = $_POST['jelszo'];
    
    $stmt = $kapcsolat->prepare("SELECT id, jelszo FROM felhasznalok WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $eredmeny = $stmt->get_result();
    $felhasznalo = $eredmeny->fetch_assoc();
    
    if ($felhasznalo && password_verify($jelszo, $felhasznalo['jelszo'])) {
        $_SESSION['felhasznalo_id'] = $felhasznalo['id'];
        header("Location: index.php");
        exit;
    } else {
        $hiba = "Hibás email vagy jelszó!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav><a href="index.php">Főoldal</a></nav>
    <main>
        <h1>Bejelentkezés</h1>
        <?php if (isset($hiba)) echo "<p style='color:red'>$hiba</p>"; ?>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="jelszo" placeholder="Jelszó" required>
            <button type="submit">Belépés</button>
        </form>
        <p>Nincs még fiókod? <a href="regisztralas.php">Regisztráció</a></p>
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