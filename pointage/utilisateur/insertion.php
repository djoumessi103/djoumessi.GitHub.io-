<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST['nom_utilisateur'];
    $mot_passe = $_POST['mot_passe'];
    $role = $_POST['role'];

    $requete = $pdo->prepare("INSERT INTO utilisateur (nom_utilisateur, mot_passe, role) VALUES (?, ?, ?)");
    $requete->execute([$nom_utilisateur, $mot_passe, $role]);

    echo "Utilisateur ajouté avec succès.";
    header('location: index.php');
}

$pdo = null;
?>
