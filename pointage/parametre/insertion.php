<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_parametre = $_POST['nom_parametre'];
    $valeur_parametre = $_POST['valeur_parametre'];

    try {
        $requete = $pdo->prepare("INSERT INTO  parametre ( nom_parametre, valeur_parametre) 
            VALUES (?, ?)");
        $requete->execute([$nom_parametre, $valeur_parametre]);

        echo "<div class='alert alert-success'>parametre ajouté avec succès.</div>";
        header('location: index.php');
        exit(); // Important: arrête l'exécution du script après la redirection
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout du parametre : " . $e->getMessage() . "</div>";
    }
}

$pdo = null;
?>