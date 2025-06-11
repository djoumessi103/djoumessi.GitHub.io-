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
    $date = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
    // Times should also be treated as strings initially
    $date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
    $type_absence = filter_input(INPUT_POST, 'type_absence', FILTER_SANITIZE_STRING);

    if (empty($idemploye)) {
        echo "Erreur : L'ID de l'employe ne peut pas être vide.";
    } elseif (empty($date)) {
        echo "Erreur : La date debut ne peut pas être vide.";
    } elseif (empty($date_fin)) {
        echo "Erreur : La date fin ne peut pas être vide.";
    } elseif (empty($type_absence)) {
        echo "Erreur : Le type d'absence ne peut pas être vide.";
    } else {
        $requete = $pdo->prepare("INSERT INTO absence (id_employe, date_debut, date_fin, type_absence) VALUES (?, ?, ?, ?)");
        try {
            $requete->execute([$idemploye, $date, $date_fin, $type_absence]);
            header('location: index.php');
            echo "Absence ajoutée avec succès.";
            exit;
        } catch (PDOException $e) {
            echo "Erreur lors de l'ajout de l'absence : " . $e->getMessage();
        }
    }
}

$pdo = null;
?>
