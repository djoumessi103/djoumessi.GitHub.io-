<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idemploye = filter_input(INPUT_POST, 'id_employe', FILTER_SANITIZE_NUMBER_INT);
    // Dates should generally be treated as strings and validated/formatted
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    // Times should also be treated as strings initially
    $heure_arrivee = filter_input(INPUT_POST, 'heure_arrivee', FILTER_SANITIZE_STRING);
    $heure_depart = filter_input(INPUT_POST, 'heure_depart', FILTER_SANITIZE_STRING);

    if (empty($idemploye)) {
        echo "Erreur : L'ID de l'employe ne peut pas être vide.";
    } elseif (empty($date)) {
        echo "Erreur : La date ne peut pas être vide.";
    } elseif (empty($heure_arrivee)) {
        echo "Erreur : L'heure d'arrivée ne peut pas être vide.";
    } elseif (empty($heure_depart)) {
        echo "Erreur : L'heure de départ ne peut pas être vide.";
    } else {
        $requete = $pdo->prepare("INSERT INTO horaire (id_employe, date, heure_arrivee, heure_depart) VALUES (?, ?, ?, ?)");
        try {
            $requete->execute([$idemploye, $date, $heure_arrivee, $heure_depart]);
            header('location: index.php');
            echo "Horaire ajoutée avec succès.";
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout de l'horaire : " . $e->getMessage();
        }
    }
}

$pdo = null;
?>
