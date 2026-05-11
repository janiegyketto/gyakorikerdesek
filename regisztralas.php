<?php
require_once 'beallitas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $felhasznalonev = $_POST['felhasznalonev'];
    $email = $_POST['email'];
    $jelszo = password_hash($_POST['jelszo'], PASSWORD_DEFAULT);
    
    $stmt = $kapcsolat->prepare("INSERT INTO felhasznalok (felhasznalonev, email, jelszo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $felhasznalonev, $email, $jelszo);
    
    if ($stmt->execute()) {
        header("Location: bejelentkezes.php");
        exit;
    } else {
        $hiba = "Ez a felhasználónév vagy email már létezik!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - GyakoriKérdések</title>
    <link rel="stylesheet" href="stilus.css">
</head>
<body>
    <nav><a href="index.php">Főoldal</a></nav>
    <main>
        <h1>Regisztráció</h1>
        <?php if (isset($hiba)) echo "<p style='color:red'>$hiba</p>"; ?>
        <form method="POST">
            <input type="text" name="felhasznalonev" placeholder="Felhasználónév" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="jelszo" placeholder="Jelszó" required>
            <button type="submit">Regisztráció</button>
        </form>
        <p>Van már fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
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