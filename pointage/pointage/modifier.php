<?php
// Database connection details
$host = 'localhost';
$dbname = 'bdpointage'; // Make sure this is the correct database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger' role='alert'>Erreur de connexion à la base de données : " . $e->getMessage() . "</div>";
    die();
}

// Fetch clients for the dropdown
$stmtEmployes = $pdo->query("SELECT id_employe, nom FROM employe");
$employes = $stmtEmployes->fetchAll(PDO::FETCH_ASSOC);

// Check if an 'id' is provided for modification
$pointage = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM pointage WHERE id_pointage = ?");
    $stmt->execute([$id]);
    $pointage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pointage) {
        echo "<div class='alert alert-warning' role='alert'>Pointage non trouvé.</div>";
        die();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_employe = $_POST['id_employe'];
    $date = $_POST['date'];
    $heure_pointage = $_POST['heure_pointage'];
    $type_pointage = $_POST['type_pointage'];

    if (isset($_GET['id'])) { // Check if we are modifying
        $id = $_GET['id'];
        $stmt = $pdo->prepare("UPDATE pointage SET id_employe = ?, date = ?, heure_pointage = ?, type_pointage = ? WHERE id_pointage = ?");
        $stmt->execute([$id_employe, $date, $heure_pointage, $type_pointage, $id]);
        echo "<div class='alert alert-success' role='alert'>Pointage modifié avec succès.</div>";
    } else { // If not modifying, it's an insert
        $stmt = $pdo->prepare("INSERT INTO pointage (id_employe, date, heure_pointage, type_pointage) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_employe, $date, $heure_pointage, $type_pointage]);
        echo "<div class='alert alert-success' role='alert'>Pointage ajouté avec succès.</div>";
    }

    // Redirect after operation
    header("Location: index.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($_GET['id']) ? 'Modifier le pointage' : 'Ajouter un pointage'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Merriweather+Sans:wght@300;400;500;600&display=swap');

        :root {
            --main-color: #22BAA0;
            --color-dark: #34425A;
            --text-grey: #B0B0B0;
        }

        * {
            margin: 0;
            padding: 0;
            text-decoration: none;
            list-style-type: none;
            box-sizing: border-box;
            font-family: 'Merriweather Sans', sans-serif;
        }

        body {
            display: flex; /* Enable flexbox for the body */
            overflow-x: hidden; /* Prevent horizontal scrollbar */
        }

        #menu-toggle {
            position: fixed;
            top: 20px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            z-index: 101; /* Ensure it's above the sidebar */
            display: none; /* Hide the checkbox itself */
        }

        #menu-toggle + label {
            position: fixed;
            top: 20px; /* Adjust as needed */
            right: 20px; /* Adjust as needed */
            z-index: 102; /* Ensure the label/button is clickable */
            display: block;
            width: 40px; /* Adjust size as needed */
            height: 40px; /* Adjust size as needed */
            background-color: var(--main-color);
            color: #fff;
            text-align: center;
            line-height: 40px;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 5px;
        }

        #menu-toggle:checked ~ .sidebar {
            left: -250px;
        }

        .sidebar {
            position: fixed;
            height: 100%;
            width: 250px;
            left: 0;
            bottom: 0;
            top: 0;
            z-index: 100;
            background: var(--color-dark);
            transition: left 300ms;
            overflow-y: auto;
        }

        .side-header {
            box-shadow: 0px 5px 5px -5px rgb(0 0 0 /10%);
            background: var(--main-color);
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .side-header h3, .side-header span {
            color: #fff;
            font-weight: 400;
        }

        .side-content {
            overflow: auto;
            padding: 1rem;
        }

        .profile {
            text-align: center;
            padding: 2rem 0rem;
        }

        .bg-img {
            background-repeat: no-repeat;
            background-size: cover;
            border-radius: 50%;
            background-size: cover;
        }

        .profile-img {
            height: 80px;
            width: 80px;
            display: inline-block;
            margin: 0 auto .5rem auto;
            border: 3px solid #899DC1;
        }

        .profile h4 {
            color: #fff;
            font-weight: 500;
        }

        .profile small {
            color: #899DC1;
            font-weight: 600;
        }

        .side-menu ul {
            text-align: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #44546E;
            padding-bottom: 1rem;
        }

        .side-menu a {
            display: block;
            padding: 1.2rem 0rem;
        }

        .side-menu a.active {
            background: #2B384E;
        }

        .side-menu a.active span, .side-menu a.active small {
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
        }

        .side-menu a span, .side-menu a small {
            color: #899DC1;
        }

        .content-wrapper {
            flex-grow: 1; /* Take remaining horizontal space */
            display: flex;
            justify-content: center; /* Center content horizontally */
            align-items: flex-start; /* Align content to the top */
            padding: 20px;
            margin-left: 250px; /* Account for the sidebar width */
            transition: margin-left 300ms;
        }

        #menu-toggle:checked ~ .content-wrapper {
            margin-left: 0; /* Adjust margin when sidebar is hidden */
        }

        .form-container {
            width: 100%;
            max-width: 1000px; /* Adjust the maximum width of the form container */
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-row {
            display: flex;
            gap: 20px; /* Space between the columns */
            margin-bottom: 15px; /* Space between rows */
        }

        .form-group {
            flex: 1; /* Each form-group takes equal width */
        }
        /* Adjust button alignment */
        .form-container button {
            margin-top: 20px;
            width: 100%; /* Make the button full width if needed */
        }
    </style>
</head>
<body>
<input type="checkbox" id="menu-toggle">
    <label for="menu-toggle"></label>
    <div class="sidebar">
        <div class="side-header">
            <h3>Tech<span>vision</span></h3>
        </div>

        <div class="side-content">
            <div class="profile">
                <div class="profile-img bg-img" style="background-image: url(img.png)"></div>
                <h4>Jessy</h4>
                <small>Art web</small>
            </div>

            <div class="side-menu">
                <ul>
                    <li>
                        <a href="http://localhost/pointage/accueil/accueil.php" class="active">
                            <span class="las la-home"></span>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li>
                        <a href="index.php">
                            <span class=""></span>
                            <small>Liste pointages</small>
                        </a>
                    </li>
                    <li>
                        <a href="pointage.php">
                            <span class=""></span>
                            <small>Ajouter pointages</small>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <span class="las la-clipboard-list"></span>
                            <small></small>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <span class="las la-tasks"></span>
                            <small></small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="container mt-5 form-container">
            <center><h1><?php echo isset($_GET['id']) ? 'Modifier le pointage' : 'Ajouter un pointage'; ?></h1></center>
            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label for="id_employe">Employé :</label>
                        <select name="id_employe" class="form-control" required>
                            <option value="">Sélectionner un employé</option>
                            <?php foreach ($employes as $employe): ?>
                                <option value="<?php echo $employe['id_employe']; ?>" <?php echo (isset($pointage['id_employe']) && $pointage['id_employe'] == $employe['id_employe']) ? 'selected' : ''; ?>><?php echo $employe['nom']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Date pointage :</label>
                        <input type="date" name="date" class="form-control" value="<?php echo isset($pointage['date']) ? $pointage['date'] : ''; ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="heure_pointage">Heure pointage :</label>
                        <input type="time" name="heure_pointage" class="form-control" step="1" value="<?php echo isset($pointage['heure_pointage']) ? $pointage['heure_pointage'] : ''; ?>" required>
                        <small class="form-text text-muted">Format HH:MM:SS</small>
                    </div>
                    <div class="form-group">
                        <label for="type_pointage">Type pointage :</label>
                        <select name="type_pointage" class="form-control" required>
                            <option value="">Sélectionner le type</option>
                            <option value="Entree" <?php echo (isset($pointage['type_pointage']) && $pointage['type_pointage'] == 'Entree') ? 'selected' : ''; ?>>Entrée</option>
                            <option value="Sortie" <?php echo (isset($pointage['type_pointage']) && $pointage['type_pointage'] == 'Sortie') ? 'selected' : ''; ?>>Sortie</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo isset($_GET['id']) ? 'Modifier' : 'Ajouter'; ?></button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>