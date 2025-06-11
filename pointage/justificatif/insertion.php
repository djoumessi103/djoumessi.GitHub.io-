<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id_absence'], $_POST['type_justificatif']) && isset($_FILES['fichier_justificatif']) && $_FILES['fichier_justificatif']['error'] === UPLOAD_ERR_OK) {
        $id_absence = $_POST['id_absence'];
        $type_justificatif = $_POST['type_justificatif'];
        $fichier = $_FILES['fichier_justificatif'];

        $nom_fichier = basename($fichier['name']);
        $chemin_temporaire = $fichier['tmp_name'];
        $dossier_destination = '../uploads/'; // Créez ce dossier sur votre serveur avec les permissions appropriées

        // Ensure the upload directory exists
        if (!is_dir($dossier_destination)) {
            if (!mkdir($dossier_destination, 0777, true)) {
                echo "Erreur : Impossible de créer le dossier de destination 'uploads/'. Veuillez vérifier les permissions.";
                exit();
            }
        }

        $chemin_fichier_destination = $dossier_destination . $nom_fichier;

        // Vérification du type de fichier (facultatif mais recommandé)
        $types_autorises = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif']; // Added GIF for broader compatibility
        if (!in_array($fichier['type'], $types_autorises)) {
            echo "Erreur : Seuls les fichiers PDF, JPEG, PNG et GIF sont autorisés.";
            exit();
        }

        // Vérification de la taille du fichier (facultatif)
        if ($fichier['size'] > 5000000) { // Exemple : 5MB (increased from 2MB)
            echo "Erreur : La taille du fichier ne doit pas dépasser 5MB.";
            exit();
        }

        // Déplacement du fichier
        if (move_uploaded_file($chemin_temporaire, $chemin_fichier_destination)) {
            // Insertion dans la base de données
            try {
                // Corrected table name to 'justificatif'
                $stmt = $pdo->prepare("INSERT INTO justificatif (id_absence, type_justificatif, fichier_justificatif) VALUES (:id_absence, :type_justificatif, :fichier_justificatif)");
                $stmt->bindParam(':id_absence', $id_absence);
                $stmt->bindParam(':type_justificatif', $type_justificatif);
                $stmt->bindParam(':fichier_justificatif', $chemin_fichier_destination); // Store the full path
                $stmt->execute();
                // Instead of echoing success message, redirect directly
                header("Location: index.php?success=1"); // Redirection avec un message de succès
                exit();
            } catch (PDOException $e) {
                echo "Erreur lors de l'enregistrement dans la base de données : " . $e->getMessage();
            }
        } else {
            echo "Erreur lors du téléchargement du fichier. Code d'erreur : " . $fichier['error'];
        }
    } else {
        // Handle cases where file upload failed or form fields are missing
        if (isset($_FILES['fichier_justificatif']) && $_FILES['fichier_justificatif']['error'] !== UPLOAD_ERR_OK) {
            echo "Erreur de téléchargement : ";
            switch ($_FILES['fichier_justificatif']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    echo "Le fichier téléchargé dépasse la taille maximale autorisée par le serveur (php.ini).";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    echo "Le fichier téléchargé dépasse la taille maximale spécifiée dans le formulaire HTML.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "Le fichier n'a été que partiellement téléchargé.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "Aucun fichier n'a été téléchargé.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Un dossier temporaire est manquant.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Échec de l'écriture du fichier sur le disque.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "Une extension PHP a arrêté le téléchargement du fichier.";
                    break;
                default:
                    echo "Erreur inconnue lors du téléchargement.";
                    break;
            }
        } else {
            echo "Veuillez remplir tous les champs du formulaire et télécharger un fichier.";
        }
    }
}
?>