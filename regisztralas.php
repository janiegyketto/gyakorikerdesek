<?php
require_once 'beallitas.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $felhasznalonev = trim($_POST['felhasznalonev']);
    $email = trim($_POST['email']);
    $jelszo = $_POST['jelszo'];

    if (!empty($felhasznalonev) && !empty($email) && !empty($jelszo)) {
        $stmt = $kapcsolat->prepare("SELECT id FROM felhasznalok WHERE felhasznalonev = ? OR email = ?");
        $stmt->bind_param("ss", $felhasznalonev, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $hiba = "A felhasználónév vagy email már foglalt!";
        } else {
            $hash = password_hash($jelszo, PASSWORD_BCRYPT);
            $stmt = $kapcsolat->prepare("INSERT INTO felhasznalok (felhasznalonev, email, jelszo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $felhasznalonev, $email, $hash);
            if ($stmt->execute()) {
                header("Location: bejelentkezes.php");
                exit;
            } else {
                $hiba = "Hiba történt a regisztráció során.";
            }
        }
    } else {
        $hiba = "Minden mező kitöltése kötelező!";
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
            <input type="email" name="email" placeholder="Email cím" required>
            <input type="password" name="jelszo" placeholder="Jelszó" required>
            <button type="submit">Regisztráció</button>
        </form>
        <p>Van már fiókod? <a href="bejelentkezes.php">Bejelentkezés</a></p>
    </main>
    <script>
        if (localStorage.getItem('dark_mode') === 'dark') document.body.classList.add('dark');
    </script>
</body>
</html>