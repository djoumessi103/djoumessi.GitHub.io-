<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Récupérer l'ID de l'horaire à supprimer
if (isset($_GET['id'])) {
    $id_horaire = $_GET['id'];

    // Récupérer les informations de l'horaire à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM horaire WHERE id_horaire = ?");
    $stmt->execute([$id_horaire]);
    $horaire = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horaire) {
        echo "Horaire non trouvé.";
        die();
    }
} else {
    echo "ID de l'horaire non spécifié.";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM horaire WHERE id_horaire = ?");
        $stmt->execute([$id_horaire]);

        // Au lieu d'afficher directement le HTML, on définit une variable JavaScript
        echo "<script>var deletionSuccess = true;</script>";
        // Rediriger vers une liste des horaires ou une autre page appropriée
        header("Location: index.php"); // Remplacez ceci par votre page de liste actuelle
        exit(); // Assurez-vous qu'aucun autre HTML n'est envoyé après la redirection


    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression de l'horaire : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des horaires</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un horaire</h1>
        <p>Êtes-vous sûr de vouloir supprimer l'horaire "<?php echo $horaire['date'] . ' ' . $horaire['heure_arrivee']; ?>"?</p>
        <form method="post">
            <button type="submit" name="Supprimer" value="Supprimer" class="btn btn-danger">Supprimer</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Vérifie si la variable JavaScript est définie et affiche le message si c'est le cas.
        if (typeof deletionSuccess !== 'undefined' && deletionSuccess === true) {
            alert("Horaire supprimé avec succès!");
        }
    </script>
</body>
</html>
