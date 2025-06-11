<?php
// Database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all parameters
    $stmt = $pdo->prepare("SELECT * FROM parametre ORDER BY nom_parametre ASC"); // Added ORDER BY
    $stmt->execute();
    $parametres = $stmt->fetchAll(PDO::FETCH_OBJ);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}

// Display status messages from redirections
$status_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_add') {
        $status_message = "<div class='alert alert-success mt-3'>Paramètre ajouté avec succès.</div>";
    } elseif ($_GET['status'] == 'success_delete') {
        $status_message = "<div class='alert alert-success mt-3'>Paramètre supprimé avec succès.</div>";
    } elseif ($_GET['status'] == 'success_update') {
        $status_message = "<div class='alert alert-success mt-3'>Paramètre modifié avec succès.</div>";
    } elseif ($_GET['status'] == 'error') {
        $status_message = "<div class='alert alert-danger mt-3'>Une erreur est survenue.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des parametres</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

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
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        #menu-toggle {
            display: none;
        }

        #menu-toggle + label {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 102;
            display: block;
            width: 40px;
            height: 40px;
            background-color: var(--main-color);
            color: #fff;
            text-align: center;
            line-height: 40px;
            font-size: 1.5rem;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        #menu-toggle + label:hover {
            background-color: #1e9a83;
        }

        #menu-toggle:checked ~ .sidebar {
            left: -250px;
        }

        #menu-toggle:checked ~ .employee-list-container {
            margin-left: 0;
            width: 100%;
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
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
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
            background-position: center;
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
            color: #899DC1;
            transition: background 0.3s, color 0.3s;
        }

        .side-menu a.active {
            background: #2B384E;
            color: #fff;
        }

        .side-menu a:hover:not(.active) {
            background: #2B384E;
            color: #fff;
        }

        .side-menu a span {
            display: block;
            text-align: center;
            font-size: 1.7rem;
            margin-bottom: 5px;
        }

        .side-menu a span, .side-menu a small {
            color: inherit;
        }

        .employee-list-container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding: 20px;
            margin-left: 250px;
            transition: margin-left 300ms;
            width: calc(100% - 250px);
        }

        .employee-list-wrapper {
            width: 100%;
            max-width: 1200px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .employee-list-wrapper h2 {
            color: var(--color-dark);
            margin-bottom: 1rem;
            text-align: center;
        }

        /* Styles for the search input group */
        .search-input-group {
            display: flex;
            margin-bottom: 1rem;
            width: 100%;
            max-width: 400px; /* Limit search bar width */
            margin-left: auto; /* Center the search bar */
            margin-right: auto; /* Center the search bar */
        }

        .search-input-group .form-control {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-color: var(--main-color); /* Match theme color */
        }

        .search-input-group .input-group-append .btn {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            background-color: var(--main-color);
            border-color: var(--main-color);
            color: white;
        }

        .employee-list-wrapper .btn-success {
            margin-bottom: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 2px solid green;
            white-space: nowrap;
        }

        .employee-list-wrapper .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .employee-list-wrapper .table th, .employee-list-wrapper .table td {
            border: 1px solid #ddd;
            padding: 0.8rem;
            text-align: left;
            vertical-align: middle;
        }

        .employee-list-wrapper .table th {
            background-color: #e9ecef;
            font-weight: bold;
            color: var(--color-dark);
        }

        .employee-list-wrapper .table .options {
            white-space: nowrap;
        }

        .employee-list-wrapper .table .btn-primary,
        .employee-list-wrapper .table .btn-danger {
            margin-right: 0.5rem;
            padding: 0.375rem 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .employee-list-wrapper .table .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .employee-list-wrapper .table .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        .employee-list-wrapper .table .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .employee-list-wrapper .table .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
        }

        .employee-list-wrapper .table tbody tr {
            background-color: #fff;
        }

        .employee-list-wrapper .table tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .employee-list-wrapper .table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Responsive table */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            #menu-toggle + label {
                display: block;
            }

            #menu-toggle:checked ~ .sidebar {
                left: 0;
            }

            .employee-list-container {
                margin-left: 0;
                width: 100%;
            }

            .employee-list-wrapper {
                padding: 10px;
            }

            .table-responsive {
                overflow-x: auto;
            }

            .employee-list-wrapper .table th,
            .employee-list-wrapper .table td {
                padding: 0.5rem;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="menu-toggle">
    <label for="menu-toggle"><span class="las la-bars"></span></label>
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
                       <a href="http://localhost/pointage/accueil/accueil.php" class="">
                            <span class="las la-home"></span>
                            <small>Dashboard</small>
                        </a>
                    </li>
                    <li>
                       <a href="index.php" class="active">
                            <span class=""></span>
                            <small>Liste des paramètres</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="employee-list-container">
        <div class="employee-list-wrapper">
            <h2>Liste des paramètres</h2>
            <?php echo $status_message; // Display status message here ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="parametre.php" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                        <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                    </svg>
                  
                </a>

                <div class="input-group search-input-group">
                    <input type="text" class="form-control" placeholder="Rechercher un paramètre..." aria-label="Rechercher un paramètre" aria-describedby="button-addon2">
                    <div class="input-group-append">
                        <button class="btn" type="button" id="button-addon2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Nom paramètre</th>
                            <th>Valeur paramètre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($parametres && count($parametres) > 0):
                            $i = 0;
                            foreach ($parametres as $parametre):
                                $i++;
                                ?>
                                <tr>
                                    <td><?= $i ?></td>
                                    <td><?= htmlspecialchars($parametre->nom_parametre) ?></td>
                                    <td><?= htmlspecialchars($parametre->valeur_parametre) ?></td>
                                    <td class="options">
                                        <a href="modifier.php?id=<?= htmlspecialchars($parametre->id_parametre) ?>" class="btn btn-primary btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir modifier ce paramètre ?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.21 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                            </svg>
                                        </a>
                                        <a href="delete.php?id=<?= htmlspecialchars($parametre->id_parametre) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce paramètre ?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" class="text-center">Aucun paramètre trouvé.</td>
                            </tr>
                        <?php
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>