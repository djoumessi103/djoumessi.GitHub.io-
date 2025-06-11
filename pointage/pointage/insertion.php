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
    $heure_pointage = filter_input(INPUT_POST, 'heure_pointage', FILTER_SANITIZE_STRING);
    $type_pointage = filter_input(INPUT_POST, 'type_pointage', FILTER_SANITIZE_STRING);

    if (empty($idemploye)) {
        echo "Erreur : L'ID de l'employe ne peut pas être vide.";
    } elseif (empty($date)) {
        echo "Erreur : La date ne peut pas être vide.";
    } elseif (empty($heure_pointage)) {
        echo "Erreur : L'heure de pointage ne peut pas être vide.";
    } elseif (empty($type_pointage)) {
        echo "Erreur : Le type de pointage ne peut pas être vide.";
    } else {
        $requete = $pdo->prepare("INSERT INTO pointage (id_employe, date, heure_pointage, type_pointage) VALUES (?, ?, ?, ?)");
        try {
            $requete->execute([$idemploye, $date, $heure_pointage, $type_pointage]);
            header('location: index.php');
            echo "Pointage ajoutée avec succès.";
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout du pointage : " . $e->getMessage();
        }
    }
}

$pdo = null;
?>
