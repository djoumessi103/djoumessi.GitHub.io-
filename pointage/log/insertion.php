<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date_heure_action = $_POST['date_heure_action'];
    $utilisateur = $_POST['utilisateur'];
    $action_effectuee = $_POST['action_effectuee'];

    try {
        $requete = $pdo->prepare("INSERT INTO log (date_heure_action, utilisateur, action_effectuee) VALUES (?, ?, ?)");
        $requete->execute([$date_heure_action, $utilisateur, $action_effectuee]);

        echo "<div class='alert alert-success'>Log ajouté avec succès.</div>";
        header('location: index.php');
        exit(); // Important: arrête l'exécution du script après la redirection
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Erreur lors de l'ajout du log : " . $e->getMessage() . "</div>";
    }
}

$pdo = null;
?>