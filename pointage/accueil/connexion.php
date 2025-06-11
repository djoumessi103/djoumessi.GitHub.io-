<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST["username"]);  //  **Use 'username' (from index.php)**
    $password = $_POST["password"];                  //  **Use 'password' (from index.php)**

    try {
        $stmt = $pdo->prepare("SELECT id_utilisateur, nom_utilisateur, role, mot_passe FROM utilisateur WHERE nom_utilisateur = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && ($password == $user["mot_passe"])) {  // **INSECURE! PASSWORD HASHING**
            $_SESSION["user_id"] = $user["id_utilisateur"];
            $_SESSION["user_role"] = $user["role"];
            $_SESSION["username"] = $user["nom_utilisateur"];

            header("Location: accueil.php");
            exit;
        } else {
            $_SESSION["error"] = "Invalid login.";
            header("Location: index.php");  // **Redirect to index.php**
            exit;
        }
    } catch (PDOException $e) {
        die("Query Error: " . $e->getMessage());
    }
}
?>
