<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=bdpointage", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initial query to fetch all logs
    $stmt = $pdo->prepare("SELECT * FROM log");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_OBJ);

} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des logs</title>
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
        }

        #menu-toggle {
            display: none;
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

        .employee-list-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            margin-left: 250px;
            transition: margin-left 300ms;
        }

        .employee-list-wrapper {
            width: 100%;
            max-width: 1200px;
        }

        .employee-list-wrapper h2 {
            color: var(--color-dark);
            margin-bottom: 1rem;
            text-align: center;
        }

        /* Style for the search input group */
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
            display: inline-block;
            border: 2px solid green;
            white-space: nowrap; /* Prevents text from wrapping if adding text to button */
        }

        .employee-list-wrapper .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }

        .employee-list-wrapper .table th, .employee-list-wrapper .table td {
            border: 1px solid #ddd;
            padding: 0.5rem;
            text-align: left;
        }

        .employee-list-wrapper .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .employee-list-wrapper .table .options {
            white-space: nowrap;
        }

        .employee-list-wrapper .table .btn-primary {
            margin-right: 0.5rem;
            border: 2px solid blue;
        }

        .employee-list-wrapper .table .btn-danger {
            margin-right: 0.5rem;
            border: 2px solid red;
        }

        #menu-toggle:checked ~ .sidebar {
            width: 60px;
        }

        #menu-toggle:checked ~ .employee-list-container {
            margin-left: 60px;
        }

        .employee-list-wrapper .table tbody tr {
            background-color: #f9f9f9;
        }

        .employee-list-wrapper .table tbody tr:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="menu-toggle">
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
                    
                </ul>
            </div>
        </div>
    </div>

    <div class="employee-list-container">
        <div class="employee-list-wrapper">
            <h2>Liste des logs</h2>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="log.php" class="btn btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-add" viewBox="0 0 16 16">
                        <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m.5-5v1h1a.5.5 0 0 1 0 1h-1v1a.5.5 0 0 1-1 0v-1h-1a.5.5 0 0 1 0-1h1v-1a.5.5 0 0 1 1 0m-2-6a3 3 0 1 1-6 0 3 3 0 0 1 6 0M8 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4"/>
                        <path d="M8.256 14a4.5 4.5 0 0 1-.229-1.004H3c.001-.246.154-.986.832-1.664C4.484 10.68 5.711 10 8 10q.39 0 .74.025c.226-.341.496-.65.804-.918Q8.844 9.002 8 9c-5 0-6 3-6 4s1 1 1 1z"/>
                    </svg>
                   
                </a>

                <div class="input-group search-input-group">
                    <input type="text" class="form-control" placeholder="Rechercher un log..." aria-label="Rechercher un log" aria-describedby="button-addon2">
                    <div class="input-group-append">
                        <button class="btn" type="button" id="button-addon2">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Date & heure action</th>
                        <th>Utilisateur</th>
                        <th>Action effectuee</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($logs != null && sizeof($logs) > 0):
                        $i = 0;
                        foreach ($logs as $log):
                            $i++;
                            ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td><?= htmlspecialchars($log->date_heure_action) ?></td>
                                <td><?= htmlspecialchars($log->utilisateur) ?></td>
                                <td><?= htmlspecialchars($log->action_effectuee) ?></td>
                                <td class="options">
                                    <a href="modifier.php?id=<?= htmlspecialchars($log->id_log) ?>" class="btn btn-primary btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir modifier ce log ?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                        </svg>
                                    </a>
                                    <a href="delete.php?id=<?= htmlspecialchars($log->id_log) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce log ?')">
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
                            <td colspan="5" class="text-center">Aucun log trouvé.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>