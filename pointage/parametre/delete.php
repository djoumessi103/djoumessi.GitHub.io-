<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

$id_parametre = null;
$parametre = null;

// Get parameter ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_parametre = $_GET['id'];

    // Fetch parameter information from database
    $stmt = $pdo->prepare("SELECT nom_parametre, valeur_parametre FROM parametre WHERE id_parametre = ?");
    $stmt->execute([$id_parametre]);
    $parametre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$parametre) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Paramètre non trouvé.</div></div>";
        die();
    }
} else {
    echo "<div class='container mt-3'><div class='alert alert-danger'>ID du paramètre non spécifié ou invalide.</div></div>";
    die();
}

// Handle deletion request (when the 'Supprimer' button is clicked)
if (isset($_POST['Supprimer'])) {
    try {
        // Prepare and execute the DELETE statement
        $stmt = $pdo->prepare("DELETE FROM parametre WHERE id_parametre = ?");
        if ($stmt->execute([$id_parametre])) {
            // Redirect to the list page after successful deletion
            header("Location: index.php?status=success_delete");
            exit(); // Important: Stop script execution after redirection
        } else {
            echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du paramètre.</div></div>";
        }
    } catch (PDOException $e) {
        echo "<div class='container mt-3'><div class='alert alert-danger'>Erreur lors de la suppression du paramètre : " . $e->getMessage() . "</div></div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supprimer un parametre</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Supprimer un parametre</h1>
        <p>Êtes-vous sûr de vouloir supprimer le parametre "<?php echo htmlspecialchars($parametre['nom_parametre'] . ' (' . $parametre['valeur_parametre'] . ')'); ?>" ?</p>
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