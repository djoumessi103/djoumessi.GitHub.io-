<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $matricule = $_POST['matricule'];
    $fonction = $_POST['fonction'];
     $departement = $_POST['departement'];

    $requete = $pdo->prepare("INSERT INTO employe (nom, prenom, matricule, fonction, departement) VALUES (?, ?, ?, ?, ?)");
    $requete->execute([$nom, $prenom, $matricule, $fonction, $departement]);

    echo "Employe ajouté avec succès.";
    header('location: index.php');
}

$pdo = null;
?>
