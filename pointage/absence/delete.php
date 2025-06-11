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
    $id_absence = $_GET['id'];

    // Récupérer les informations de l'horaire à partir de la base de données
    $stmt = $pdo->prepare("SELECT * FROM absence WHERE id_absence = ?");
    $stmt->execute([$id_absence]);
    $absence = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$absence) {
        echo "Absence non trouvé.";
        die();
    }
} else {
    echo "ID de l'absence non spécifié.";
    die();
}

// Traitement de la suppression
if (isset($_POST['Supprimer'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM absence WHERE id_absence = ?");
        $stmt->execute([$id_absence]);

        echo "<div class='container mt-3'><div class='alert alert-success'>Absence supprimé avec succès.</div></div>";
        // Redirect to a list of suppliers or another appropriate page
         header("Location: index.php"); // Replace with your actual list page
        // exit;

    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression de l'absence : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des absences</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer une absence</h1>
        <p>Êtes-vous sûr de vouloir supprimer l'absence "<?php echo $absence['date_debut'] . ' ' . $absence['type_absence']; ?>"?</p>
        <form method="post">
            <button type="submit" name="Supprimer" value="Supprimer" class="btn btn-danger">Supprimer</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
